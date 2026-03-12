<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\CidadaoController;
use App\Http\Controllers\EditoraController;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\RequisicaoController;
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

        // Preco esta cifrado; o calculo do medio precisa de desserializar no PHP.
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
    Route::patch('/requisicoes/{requisicao}/confirmar-entrega', [RequisicaoController::class, 'confirmarEntrega'])
        ->middleware('admin')
        ->name('requisicoes.confirmar-entrega');
    Route::patch('/requisicoes/{requisicao}/confirmar-entrega', [RequisicaoController::class, 'confirmarEntrega'])
    ->name('requisicoes.confirmar-entrega');

    Route::patch('/requisicoes/{requisicao}/confirmar-devolucao', [RequisicaoController::class, 'confirmarDevolucao'])
    ->name('requisicoes.confirmar-devolucao');

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
});
