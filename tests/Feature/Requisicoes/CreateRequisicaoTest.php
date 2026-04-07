<?php

use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;

it('creates a book requisition successfully', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $cidadao = User::factory()->create([
        'role' => User::ROLE_CIDADAO,
    ]);

    $editora = Editora::query()->create(['nome' => 'Editora Teste']);
    $autor = Autor::query()->create(['nome' => 'Autor Teste']);

    $livro = Livro::query()->create([
        'isbn' => 'ISBN-TESTE-001',
        'nome' => 'Livro Teste',
        'editora_id' => $editora->id,
        'sinopse' => 'Sinopse teste',
        'preco' => '10.00',
    ]);

    $livro->autores()->sync([$autor->id]);

    $response = $this->actingAs($admin)->post(route('requisicoes.store'), [
        'livro_id' => $livro->id,
        'cidadao_id' => $cidadao->id,
    ]);

    $response
        ->assertRedirect(route('requisicoes.index'))
        ->assertSessionHas('status');

    $this->assertDatabaseHas('requisicoes', [
        'livro_id' => $livro->id,
        'cidadao_id' => $cidadao->id,
        'estado' => Requisicao::ESTADO_PENDENTE_ENTREGA,
    ]);

    $requisicao = Requisicao::query()->firstOrFail();

    expect($requisicao->cidadao_nome)->toBe($cidadao->name);
    expect($requisicao->cidadao_email)->toBe($cidadao->email);
});
