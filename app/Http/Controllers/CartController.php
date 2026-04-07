<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Livro;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = Cart::query()
            ->firstOrCreate(['user_id' => $request->user()->id])
            ->load('items.livro');

        return view('cart.index', compact('cart'));
    }

    public function store(Request $request, Livro $livro): RedirectResponse
    {
        $cart = Cart::query()->firstOrCreate([
            'user_id' => $request->user()->id,
        ]);

        $item = $cart->items()->firstOrCreate(
            ['livro_id' => $livro->id],
            ['quantity' => 1]
        );

        if (! $item->wasRecentlyCreated) {
            $item->increment('quantity');

            ActivityLogger::log(
                userId: $request->user()->id,
                module: 'cart',
                objectId: $cart->id,
                action: 'update_quantity',
                description: 'Quantidade aumentada para o livro ID ' . $livro->id,
                request: $request
            );
        } else {
            ActivityLogger::log(
                userId: $request->user()->id,
                module: 'cart',
                objectId: $cart->id,
                action: 'add_item',
                description: 'Livro adicionado ao carrinho ID ' . $livro->id,
                request: $request
            );
        }

        $cart->touch();

        return back()->with('success', 'Livro adicionado ao carrinho com sucesso.');
    }

    public function destroy(Request $request, Livro $livro): RedirectResponse
    {
        $cart = Cart::query()
            ->where('user_id', $request->user()->id)
            ->first();

        if ($cart) {
            $cart->items()->where('livro_id', $livro->id)->delete();
            $cart->touch();

            ActivityLogger::log(
                userId: $request->user()->id,
                module: 'cart',
                objectId: $cart->id,
                action: 'remove_item',
                description: 'Livro removido do carrinho ID ' . $livro->id,
                request: $request
            );
        }

        return back()->with('success', 'Livro removido do carrinho.');
    }
}
