<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Livro;
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
        }

        return back()->with('success', 'Livro removido do carrinho.');
    }
}
