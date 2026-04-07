<?php

use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Carbon\CarbonImmutable;

it('shows only the logged in citizen requisitions', function () {
    $cidadaoA = User::factory()->create([
        'role' => User::ROLE_CIDADAO,
        'name' => 'Cidadao A',
    ]);

    $cidadaoB = User::factory()->create([
        'role' => User::ROLE_CIDADAO,
        'name' => 'Cidadao B',
    ]);

    $editora = Editora::query()->create(['nome' => 'Editora Listagem']);
    $autor = Autor::query()->create(['nome' => 'Autor Listagem']);

    $livroA = Livro::query()->create([
        'isbn' => 'ISBN-LISTA-001',
        'nome' => 'Livro A',
        'editora_id' => $editora->id,
        'sinopse' => 'Sinopse A',
        'preco' => '10.00',
    ]);

    $livroB = Livro::query()->create([
        'isbn' => 'ISBN-LISTA-002',
        'nome' => 'Livro B',
        'editora_id' => $editora->id,
        'sinopse' => 'Sinopse B',
        'preco' => '11.00',
    ]);

    $livroA->autores()->sync([$autor->id]);
    $livroB->autores()->sync([$autor->id]);

    Requisicao::query()->create([
        'numero_sequencial' => 1,
        'estado' => Requisicao::ESTADO_PENDENTE_ENTREGA,
        'livro_id' => $livroA->id,
        'cidadao_id' => $cidadaoA->id,
        'cidadao_nome' => $cidadaoA->name,
        'cidadao_email' => $cidadaoA->email,
        'cidadao_foto_path' => $cidadaoA->profile_photo_path,
        'data_requisicao' => CarbonImmutable::today(),
        'data_prevista_entrega' => CarbonImmutable::today()->addDays(5),
    ]);

    Requisicao::query()->create([
        'numero_sequencial' => 2,
        'estado' => Requisicao::ESTADO_PENDENTE_ENTREGA,
        'livro_id' => $livroB->id,
        'cidadao_id' => $cidadaoB->id,
        'cidadao_nome' => $cidadaoB->name,
        'cidadao_email' => $cidadaoB->email,
        'cidadao_foto_path' => $cidadaoB->profile_photo_path,
        'data_requisicao' => CarbonImmutable::today(),
        'data_prevista_entrega' => CarbonImmutable::today()->addDays(5),
    ]);

    $response = $this->actingAs($cidadaoA)->get(route('requisicoes.index'));

    $response->assertOk();
    $response->assertSee('Livro A');
    $response->assertDontSee('Livro B');
});
