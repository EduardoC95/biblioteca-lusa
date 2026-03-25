<?php

namespace App\Notifications\Cidadao;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewModeradaNotification extends Notification
{
    use Queueable;

    public function __construct(public Review $review)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Atualização da sua review')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('A sua review ao livro "' . $this->review->livro->nome . '" foi analisada.')
            ->line('Estado: ' . ucfirst($this->review->estado));

        if (
            $this->review->estado === Review::ESTADO_RECUSADO
            && $this->review->justificacao_recusa
        ) {
            $mail->line('Justificação: ' . $this->review->justificacao_recusa);
        }

        return $mail;
    }
}
