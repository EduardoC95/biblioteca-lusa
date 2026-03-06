@php
    $livro = $requisicao->livro;
    $capaUrl = $livro && $livro->capa_imagem ? \Illuminate\Support\Facades\Storage::url($livro->capa_imagem) : null;
@endphp

<h2>{{ $destinoAdmin ? 'Nova requisição registada' : 'A sua requisição foi confirmada' }}</h2>
<p><strong>Número:</strong> #{{ $requisicao->numero_sequencial }}</p>
<p><strong>Cidadão:</strong> {{ $requisicao->cidadao_nome }} ({{ $requisicao->cidadao_email }})</p>
<p><strong>Livro:</strong> {{ $livro?->nome }}</p>
<p><strong>Editora:</strong> {{ $livro?->editora?->nome ?? '-' }}</p>
<p><strong>Data da requisição:</strong> {{ $requisicao->data_requisicao?->format('d/m/Y') }}</p>
<p><strong>Data prevista de entrega:</strong> {{ $requisicao->data_prevista_entrega?->format('d/m/Y') }}</p>

@if ($capaUrl)
    <p><img src="{{ $capaUrl }}" alt="Capa do livro" style="max-width: 180px; border-radius: 8px;"></p>
@endif

