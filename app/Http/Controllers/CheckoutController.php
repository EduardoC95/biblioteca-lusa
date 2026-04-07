<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $cart = Cart::query()
            ->where('user_id', $request->user()->id)
            ->with('items.livro')
            ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'O carrinho está vazio.');
        }

        return view('checkout.show', compact('cart'));
    }

    public function process(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'delivery_name' => ['required', 'string', 'max:255'],
            'delivery_email' => ['required', 'email', 'max:255'],
            'delivery_phone' => ['nullable', 'string', 'max:50'],
            'delivery_address' => ['required', 'string', 'max:255'],
            'delivery_postal_code' => ['required', 'string', 'max:30'],
            'delivery_city' => ['required', 'string', 'max:255'],
        ]);

        $cart = Cart::query()
            ->where('user_id', $request->user()->id)
            ->with('items.livro')
            ->firstOrFail();

        abort_if($cart->items->isEmpty(), 422, 'Carrinho vazio.');

        $totalAmount = $cart->items->sum(function ($item) {
            return ((int) ($item->livro->preco * 100)) * $item->quantity;
        });

        $order = Order::create([
            'user_id' => $request->user()->id,
            'status' => Order::STATUS_PENDING,
            'payment_status' => Order::PAYMENT_PENDING,
            'delivery_name' => $data['delivery_name'],
            'delivery_email' => $data['delivery_email'],
            'delivery_phone' => $data['delivery_phone'] ?? null,
            'delivery_address' => $data['delivery_address'],
            'delivery_postal_code' => $data['delivery_postal_code'],
            'delivery_city' => $data['delivery_city'],
            'total_amount' => $totalAmount,
            'currency' => 'eur',
        ]);

        foreach ($cart->items as $item) {
            $unitPrice = (int) ($item->livro->preco * 100);
            $subtotal = $unitPrice * $item->quantity;

            $order->items()->create([
                'livro_id' => $item->livro->id,
                'book_title' => $item->livro->nome,
                'book_isbn' => $item->livro->isbn,
                'quantity' => $item->quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ]);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
            'metadata' => [
                'order_id' => (string) $order->id,
                'user_id' => (string) $request->user()->id,
            ],
            'line_items' => $order->items->map(function ($item) {
                return [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $item->book_title,
                        ],
                        'unit_amount' => $item->unit_price,
                    ],
                    'quantity' => $item->quantity,
                ];
            })->values()->toArray(),
        ]);

        $order->update([
            'stripe_checkout_session_id' => $session->id,
        ]);

        return redirect($session->url);
    }

    public function success(Request $request): View
    {
        return view('checkout.success');
    }

    public function cancel(Request $request): View
    {
        return view('checkout.cancel');
    }
}
