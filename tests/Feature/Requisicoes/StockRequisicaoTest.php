<?php

use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Carbon\CarbonImmutable;

it('does not allow creating a requisition for a book already in an active requisition', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $cidadaoA = User::factory()->create([
        'role' => User::ROLE_CIDADAO,
    ]);

    $cidadaoB = User::factory()->create([
        'role' => User::ROLE_CIDADAO,
    ]);

    $editora = Editora::query()->create(['nome' => 'Editora Stock']);
    $autor = Autor::query()->create(['nome' => 'Autor Stock']);

    $livro = Livro::query()->create([
        'isbn' => 'ISBN-STOCK-001',
        'nome' => 'Livro Sem Disponibilidade',
        'editora_id' => $editora->id,
        'sinopse' => 'Sinopse stock',
        'preco' => '15.00',
    ]);

    $livro->autores()->sync([$autor->id]);

    Requisicao::query()->create([
        'numero_sequencial' => 1,
        'estado' => Requisicao::ESTADO_ATIVA,
        'livro_id' => $livro->id,
        'cidadao_id' => $cidadaoA->id,
        'cidadao_nome' => $cidadaoA->name,
        'cidadao_email' => $cidadaoA->email,
        'cidadao_foto_path' => $cidadaoA->profile_photo_path,
        'data_requisicao' => CarbonImmutable::today()->subDays(6),
        'data_prevista_entrega' => CarbonImmutable::today()->subDays(5),
        'data_entrega_real' => CarbonImmutable::today()->subDays(5),
        'data_devolucao_prevista' => CarbonImmutable::today(),
    ]);

    $response = $this->actingAs($admin)
        ->from(route('requisicoes.index'))
        ->post(route('requisicoes.store'), [
            'livro_id' => $livro->id,
            'cidadao_id' => $cidadaoB->id,
        ]);

    $response
        ->assertRedirect(route('requisicoes.index'))
        ->assertSessionHasErrors('livro_id');

    expect(
        Requisicao::where('livro_id', $livro->id)->count()
    )->toBe(1);
});
