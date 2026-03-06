<?php

namespace App\Mail;

use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequisicaoCriadaMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Requisicao $requisicao,
        public bool $destinoAdmin,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nova requisição #' . $this->requisicao->numero_sequencial,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.requisicoes.criada',
        );
    }
}
