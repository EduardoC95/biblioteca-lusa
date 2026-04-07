<?php

use App\Models\Requisicao;
use App\Models\User;

it('does not create a requisition without a valid book', function () {
    $admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $cidadao = User::factory()->create([
        'role' => User::ROLE_CIDADAO,
    ]);

    $response = $this->actingAs($admin)
        ->from(route('requisicoes.index'))
        ->post(route('requisicoes.store'), [
            'livro_id' => 999999,
            'cidadao_id' => $cidadao->id,
        ]);

    $response
        ->assertRedirect(route('requisicoes.index'))
        ->assertSessionHasErrors('livro_id');

    expect(Requisicao::count())->toBe(0);
});
