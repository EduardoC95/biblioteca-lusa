@php
    $livro = $requisicao->livro;
@endphp

<h2>Lembrete de entrega</h2>
<p>A requisição <strong>#{{ $requisicao->numero_sequencial }}</strong> vence amanhã.</p>
<p><strong>Livro:</strong> {{ $livro?->nome }}</p>
<p><strong>Data prevista de entrega:</strong> {{ $requisicao->data_prevista_entrega?->format('d/m/Y') }}</p>
<p>Por favor, entregue o livro dentro do prazo.</p>

