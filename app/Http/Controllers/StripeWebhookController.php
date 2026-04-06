<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
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

            $order = Order::query()
                ->where('id', $session->metadata->order_id ?? null)
                ->first();

            if ($order && $order->status !== 'paid') {
                $order->update([
                    'status' => 'paid',
                    'stripe_payment_intent_id' => $session->payment_intent ?? null,
                    'paid_at' => now(),
                ]);

                Cart::query()
                    ->where('user_id', $order->user_id)
                    ->first()?->items()->delete();
            }
        }

        return response('Webhook handled.', 200);
    }
}
