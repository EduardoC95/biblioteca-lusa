<?php

use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Carbon\CarbonImmutable;

it('allows returning a borrowed book', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $cidadao = User::factory()->create([
        'role' => User::ROLE_CIDADAO,
    ]);

    $editora = Editora::query()->create(['nome' => 'Editora Devolucao']);
    $autor = Autor::query()->create(['nome' => 'Autor Devolucao']);

    $livro = Livro::query()->create([
        'isbn' => 'ISBN-DEV-001',
        'nome' => 'Livro Devolucao',
        'editora_id' => $editora->id,
        'sinopse' => 'Sinopse devolucao',
        'preco' => '12.00',
    ]);

    $livro->autores()->sync([$autor->id]);

    $requisicao = Requisicao::query()->create([
        'numero_sequencial' => 1,
        'estado' => Requisicao::ESTADO_ATIVA,
        'livro_id' => $livro->id,
        'cidadao_id' => $cidadao->id,
        'cidadao_nome' => $cidadao->name,
        'cidadao_email' => $cidadao->email,
        'cidadao_foto_path' => $cidadao->profile_photo_path,
        'data_requisicao' => CarbonImmutable::today()->subDays(8),
        'data_prevista_entrega' => CarbonImmutable::today()->subDays(7),
        'data_entrega_real' => CarbonImmutable::today()->subDays(5),
        'data_devolucao_prevista' => CarbonImmutable::today(),
    ]);

    $response = $this->actingAs($admin)->patch(route('requisicoes.confirmar-devolucao', $requisicao), [
    'data_devolucao_real' => CarbonImmutable::today()->toDateString(),
    ]);

    $response->assertRedirect();

    $requisicao->refresh();

    expect($requisicao->estado)->toBe(Requisicao::ESTADO_DEVOLVIDA);
    expect($requisicao->data_devolucao_real?->format('Y-m-d'))->toBe(CarbonImmutable::today()->toDateString());
    expect($requisicao->devolucao_confirmada_por_admin_id)->toBe($admin->id);
});
