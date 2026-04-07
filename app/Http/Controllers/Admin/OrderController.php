<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = Order::with(['user', 'items'])
            ->latest()
            ->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function approve(Order $order): RedirectResponse
    {
        $order->update([
            'status' => Order::STATUS_CONFIRMED,
            'payment_status' => Order::PAYMENT_PAID,
            'paid_at' => $order->paid_at ?? now(),
        ]);

        ActivityLogger::log(
            userId: Auth::id(),
            module: 'orders',
            objectId: $order->id,
            action: 'approve',
            description: 'Pagamento/encomenda validado manualmente por admin',
            request: request()
        );

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Compra validada com sucesso.');
    }

    public function reject(Order $order): RedirectResponse
    {
        $order->update([
            'status' => Order::STATUS_CANCELLED,
            'payment_status' => Order::PAYMENT_FAILED,
        ]);

        ActivityLogger::log(
            userId: Auth::id(),
            module: 'orders',
            objectId: $order->id,
            action: 'reject',
            description: 'Compra recusada manualmente por admin',
            request: request()
        );

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Compra recusada com sucesso.');
    }
}
