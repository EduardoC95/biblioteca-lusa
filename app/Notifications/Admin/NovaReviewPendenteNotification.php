<?php

namespace App\Notifications\Admin;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NovaReviewPendenteNotification extends Notification
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
        $url = route('admin.reviews.show', $this->review);

        return (new MailMessage)
            ->subject('Nova review pendente de moderação')
            ->greeting('Olá!')
            ->line('Foi submetida uma nova review.')
            ->line('Cidadão: ' . $this->review->user->name)
            ->line('Email: ' . $this->review->user->email)
            ->line('Livro: ' . $this->review->livro->titulo)
            ->action('Ver detalhe da review', $url);
    }
}
