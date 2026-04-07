<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\CidadaoController;
use App\Http\Controllers\EditoraController;
use App\Http\Controllers\GoogleBooksController;
use App\Http\Controllers\GoogleBooksImportController;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\PesquisaLivrosController;
use App\Http\Controllers\RequisicaoController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\AlertaDisponibilidadeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeWebhookController;
use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::view('/', 'landing')->name('landing');

Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');
Route::get('/catalogo/{livro}', [CatalogoController::class, 'show'])->name('catalogo.show');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $temRequisicoes = Schema::hasColumn('livros', 'total_requisicoes');

        $livrosCount = Livro::count();
        $autoresCount = Autor::count();

        $editorasOrdenadas = Editora::query()
            ->withCount('livros')
            ->orderByDesc('livros_count')
            ->get();

        $precoMedio = Livro::query()->get(['id', 'preco'])
            ->avg(fn (Livro $livro): float => (float) $livro->preco);

        $livrosMaisRequisitados = $temRequisicoes
            ? Livro::query()->with('editora')->orderByDesc('total_requisicoes')->take(5)->get()
            : Livro::query()->with('editora')->latest()->take(5)->get();

        return view('dashboard', [
            'livrosCount' => $livrosCount,
            'autoresCount' => $autoresCount,
            'editorasCount' => $editorasOrdenadas->count(),
            'precoMedio' => $precoMedio,
            'livrosMaisRequisitados' => $livrosMaisRequisitados,
            'topEditora' => $editorasOrdenadas->first(),
            'livrosPorEditora' => $editorasOrdenadas->take(6),
            'temRequisicoes' => $temRequisicoes,
        ]);
    })->name('dashboard');

    Route::get('/requisicoes', [RequisicaoController::class, 'index'])->name('requisicoes.index');
    Route::post('/requisicoes', [RequisicaoController::class, 'store'])->name('requisicoes.store');
    Route::get('/requisicoes/{requisicao}', [RequisicaoController::class, 'show'])->name('requisicoes.show');

    Route::patch('/requisicoes/{requisicao}/confirmar-entrega', [RequisicaoController::class, 'confirmarEntrega'])
        ->middleware('admin')
        ->name('requisicoes.confirmar-entrega');

    Route::patch('/requisicoes/{requisicao}/confirmar-devolucao', [RequisicaoController::class, 'confirmarDevolucao'])
        ->name('requisicoes.confirmar-devolucao');

    Route::post('/livros/{livro}/alerta-disponibilidade', [AlertaDisponibilidadeController::class, 'store'])
        ->name('livros.alerta-disponibilidade');

    Route::middleware('admin')->group(function () {
        Route::get('/livros/exportar/excel', [LivroController::class, 'export'])->name('livros.export');
        Route::resource('livros', LivroController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);

        Route::get('/cidadaos', [CidadaoController::class, 'index'])->name('cidadaos.index');
        Route::get('/cidadaos/{cidadao}', [CidadaoController::class, 'show'])->name('cidadaos.show');
        Route::post('/admins', [AdminUserController::class, 'store'])->name('admins.store');
    });

    Route::resource('livros', LivroController::class)
        ->only(['index', 'show'])
        ->where(['livro' => '[0-9]+']);

    Route::resource('autores', AutorController::class)
        ->parameters(['autores' => 'autor']);

    Route::resource('editoras', EditoraController::class);

    Route::get('/google-books/pesquisar', [GoogleBooksController::class, 'search'])
        ->name('google-books.search');

    Route::post('/google-books/importar/{volumeId}', [GoogleBooksImportController::class, 'store'])
        ->name('google-books.import');

    Route::get('/livros/pesquisa-unificada', [PesquisaLivrosController::class, 'index'])
        ->name('livros.pesquisa-unificada');

    Route::post('/requisicoes/{requisicao}/review', [ReviewController::class, 'store'])
        ->name('reviews.store');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/{review}', [AdminReviewController::class, 'show'])->name('reviews.show');
        Route::patch('/reviews/{review}', [AdminReviewController::class, 'update'])->name('reviews.update');
    });

    Route::middleware(['auth'])->group(function () {
    Route::get('/admin/compras', [OrderController::class, 'index'])
        ->name('admin.orders.index');

    Route::patch('/admin/compras/{order}/approve', [OrderController::class, 'approve'])
        ->name('admin.orders.approve');

    Route::patch('/admin/compras/{order}/reject', [OrderController::class, 'reject'])
        ->name('admin.orders.reject');
    });

    Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/livros/{livro}', [CartController::class, 'store'])->name('cart.store');
    Route::delete('/cart/livros/{livro}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/sucesso', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancelado', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
    });

    Route::post('/stripe/webhook', StripeWebhookController::class)
    ->name('stripe.webhook');
});
