<?php

namespace App\Notifications;

use App\Models\Livro;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LivroDisponivelNotification extends Notification
{
    use Queueable;

    public function __construct(public Livro $livro)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Livro disponível para requisição')
            ->greeting('Olá ' . $notifiable->name . '!')
            ->line('O livro que pretendia já se encontra disponível para requisição.')
            ->line('Livro: ' . $this->livro->nome)
            ->action('Ver livro', route('livros.show', $this->livro))
            ->line('Pode agora aceder ao catálogo e efetuar a requisição.');
    }
}
