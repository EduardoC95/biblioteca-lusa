<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmarEntregaRequest;
use App\Http\Requests\StoreRequisicaoRequest;
use App\Mail\RequisicaoCriadaMail;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RequisicaoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Requisicao::query()
            ->with(['livro.editora', 'cidadao'])
            ->orderByDesc('created_at');

        if (! $request->user()->isAdmin()) {
            $query->where('cidadao_id', $request->user()->id);
        }

        $requisicoes = $query->paginate(12)->withQueryString();

        $indicadoresBase = Requisicao::query();

        $indicadores = [
            'ativas' => (clone $indicadoresBase)->ativas()->count(),

            'ultimos_30_dias' => (clone $indicadoresBase)
                ->whereDate('data_requisicao', '>=', CarbonImmutable::today()->subDays(30))
                ->count(),

            'entregues_hoje' => (clone $indicadoresBase)
                ->whereDate('data_devolucao_real', CarbonImmutable::today())
                ->count(),
        ];

        $livrosDisponiveis = Livro::query()
            ->whereDoesntHave('requisicoes', fn ($q) => $q->ativas())
            ->orderBy('id')
            ->get();

        $cidadaos = User::query()
            ->where('role', User::ROLE_CIDADAO)
            ->orderBy('name')
            ->get();

        return view('requisicoes.index', [
            'requisicoes' => $requisicoes,
            'indicadores' => $indicadores,
            'livrosDisponiveis' => $livrosDisponiveis,
            'cidadaos' => $cidadaos,
            'livroPreSelecionado' => $request->integer('livro_id') ?: null,
        ]);
    }

    public function store(StoreRequisicaoRequest $request): RedirectResponse
    {
        $livro = Livro::query()->findOrFail((int) $request->input('livro_id'));
        $cidadao = User::query()->findOrFail($request->cidadaoId());

        $requisicao = DB::transaction(function () use ($livro, $cidadao): Requisicao {
            $livroAtivo = Requisicao::query()
                ->where('livro_id', $livro->id)
                ->ativas()
                ->exists();

            if ($livroAtivo) {
                throw ValidationException::withMessages([
                    'livro_id' => 'Este livro já está numa requisição ativa.',
                ]);
            }

            $requisicoesAtivasCidadao = Requisicao::query()
                ->where('cidadao_id', $cidadao->id)
                ->ativas()
                ->count();

            if ($requisicoesAtivasCidadao >= 3) {
                throw ValidationException::withMessages([
                    'cidadao_id' => 'Cada cidadão só pode ter 3 livros requisitados em simultâneo.',
                ]);
            }

            $numero = ((int) Requisicao::query()->max('numero_sequencial')) + 1;

            $hoje = CarbonImmutable::today();

            $requisicao = Requisicao::query()->create([
                'numero_sequencial' => $numero,
                'estado' => Requisicao::ESTADO_PENDENTE_ENTREGA,

                'livro_id' => $livro->id,
                'cidadao_id' => $cidadao->id,

                'cidadao_nome' => $cidadao->name,
                'cidadao_email' => $cidadao->email,
                'cidadao_foto_path' => $cidadao->profile_photo_path,

                'data_requisicao' => $hoje,
                'data_prevista_entrega' => $hoje->addDays(5),
            ]);

            $livro->increment('total_requisicoes');

            return $requisicao;
        });

        $requisicao->load(['livro.editora', 'cidadao']);

        Mail::to($cidadao->email)->send(new RequisicaoCriadaMail($requisicao, false));

        $adminEmails = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->pluck('email')
            ->all();

        if ($adminEmails !== []) {
            Mail::to($adminEmails)->send(new RequisicaoCriadaMail($requisicao, true));
        }

        return redirect()
            ->route('requisicoes.index')
            ->with('status', 'Requisição criada com sucesso.');
    }

    public function confirmarEntrega(ConfirmarEntregaRequest $request, Requisicao $requisicao): RedirectResponse
    {
        if ($requisicao->estado !== Requisicao::ESTADO_PENDENTE_ENTREGA) {
            return back()->with('status', 'Esta requisição já foi entregue.');
        }

        $dataEntrega = CarbonImmutable::parse($request->validated('data_real_entrega'));

        $requisicao->update([
            'data_entrega_real' => $dataEntrega,
            'data_devolucao_prevista' => $dataEntrega->addDays(5),
            'estado' => Requisicao::ESTADO_ATIVA,
        ]);

        return back()->with('status', 'Entrega ao cidadão confirmada.');
    }

    public function confirmarDevolucao(Request $request, Requisicao $requisicao): RedirectResponse
    {
        if ($requisicao->estado !== Requisicao::ESTADO_ATIVA) {
            return back()->with('status', 'Esta requisição já foi devolvida.');
        }

        $dataDevolucao = CarbonImmutable::parse($request->input('data_devolucao_real'));

        $dataEntrega = CarbonImmutable::instance($requisicao->data_entrega_real);

        $requisicao->update([
            'data_devolucao_real' => $dataDevolucao,
            'dias_decorridos' => $dataEntrega->diffInDays($dataDevolucao),
            'estado' => Requisicao::ESTADO_DEVOLVIDA,
            'devolucao_confirmada_por_admin_id' => $request->user()->id,
        ]);

        return back()->with('status', 'Devolução confirmada com sucesso.');
    }

    public function show(Requisicao $requisicao): View
    {
        $requisicao->load([
            'livro',
            'review',
        ]);

        return view('requisicoes.show', compact('requisicao'));
    }
}
