<?php

namespace Tests\Feature;

use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequisicoesTest extends TestCase
{
    use RefreshDatabase;

    public function test_cidadao_nao_pode_abrir_criacao_de_livro(): void
    {
        $cidadao = User::factory()->create(['role' => User::ROLE_CIDADAO]);

        $response = $this->actingAs($cidadao)->get(route('livros.create'));

        $response->assertForbidden();
    }

    public function test_cidadao_so_pode_ter_tres_requisicoes_ativas(): void
    {
        $cidadao = User::factory()->create(['role' => User::ROLE_CIDADAO]);

        $livros = collect(range(1, 4))->map(fn ($i) => $this->criarLivro("Livro {$i}"));

        foreach ($livros->take(3) as $livro) {
            $this->actingAs($cidadao)
                ->post(route('requisicoes.store'), ['livro_id' => $livro->id])
                ->assertRedirect(route('requisicoes.index'));
        }

        $this->actingAs($cidadao)
            ->from(route('requisicoes.index'))
            ->post(route('requisicoes.store'), ['livro_id' => $livros[3]->id])
            ->assertRedirect(route('requisicoes.index'))
            ->assertSessionHasErrors('cidadao_id');

        $this->assertSame(3, Requisicao::query()->where('cidadao_id', $cidadao->id)->count());
    }

    public function test_livro_indisponivel_nao_pode_ser_requisitado_por_outro_cidadao(): void
    {
        $livro = $this->criarLivro('Livro Unico');
        $cidadao1 = User::factory()->create(['role' => User::ROLE_CIDADAO]);
        $cidadao2 = User::factory()->create(['role' => User::ROLE_CIDADAO]);

        $this->actingAs($cidadao1)
            ->post(route('requisicoes.store'), ['livro_id' => $livro->id])
            ->assertRedirect(route('requisicoes.index'));

        $this->actingAs($cidadao2)
            ->from(route('requisicoes.index'))
            ->post(route('requisicoes.store'), ['livro_id' => $livro->id])
            ->assertRedirect(route('requisicoes.index'))
            ->assertSessionHasErrors('livro_id');
    }

    public function test_admin_pode_confirmar_entrega_e_calcular_dias_decorridos(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $cidadao = User::factory()->create(['role' => User::ROLE_CIDADAO]);
        $livro = $this->criarLivro('Livro Admin');

        CarbonImmutable::setTestNow('2026-03-01');

        $this->actingAs($admin)
            ->post(route('requisicoes.store'), ['livro_id' => $livro->id, 'cidadao_id' => $cidadao->id])
            ->assertRedirect(route('requisicoes.index'));

        $requisicao = Requisicao::query()->firstOrFail();

        $this->actingAs($admin)
            ->patch(route('requisicoes.confirmar-entrega', $requisicao), ['data_real_entrega' => '2026-03-06'])
            ->assertRedirect();

        $requisicao->refresh();

        $this->assertSame('2026-03-06', $requisicao->data_real_entrega?->format('Y-m-d'));
        $this->assertSame(5, $requisicao->dias_decorridos);
        $this->assertSame($admin->id, $requisicao->devolucao_confirmada_por_admin_id);

        CarbonImmutable::setTestNow();
    }

    private function criarLivro(string $nome): Livro
    {
        $editora = Editora::query()->create(['nome' => 'Editora '.$nome]);
        $autor = Autor::query()->create(['nome' => 'Autor '.$nome]);

        $livro = Livro::query()->create([
            'isbn' => 'ISBN-'.$nome,
            'nome' => $nome,
            'editora_id' => $editora->id,
            'sinopse' => 'Sinopse',
            'preco' => '10.00',
        ]);

        $livro->autores()->sync([$autor->id]);

        return $livro;
    }
}
