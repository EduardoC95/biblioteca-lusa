<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (UnexpectedValueException|SignatureVerificationException $e) {
            return response('Invalid webhook.', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            if (($session->payment_status ?? null) !== 'paid') {
                return response('Payment not completed.', 200);
            }

            $orderId = $session->metadata->order_id ?? null;

            if ($orderId) {
                $order = Order::find($orderId);

                if ($order && $order->payment_status !== Order::PAYMENT_PAID) {
                    $order->update([
                        'payment_status' => Order::PAYMENT_PAID,
                        'status' => Order::STATUS_CONFIRMED,
                        'stripe_payment_intent_id' => $session->payment_intent ?? null,
                        'paid_at' => now(),
                    ]);

                    ActivityLogger::log(
                        userId: $order->user_id,
                        module: 'orders',
                        objectId: $order->id,
                        action: 'payment_confirmed',
                        description: 'Pagamento confirmado via Stripe',
                        request: $request
                    );

                    $cart = Cart::query()
                        ->where('user_id', $order->user_id)
                        ->first();

                    if ($cart) {
                        $cart->update([
                            'converted_at' => now(),
                        ]);

                        $cart->items()->delete();
                    }
                }
            }
        }

        return response('Webhook handled.', 200);
    }
}
