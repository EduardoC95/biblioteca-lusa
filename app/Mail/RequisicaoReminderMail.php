<?php

namespace App\Mail;

use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RequisicaoReminderMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public Requisicao $requisicao)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Lembrete de entrega - Requisição #' . $this->requisicao->numero_sequencial,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.requisicoes.reminder',
        );
    }
}
