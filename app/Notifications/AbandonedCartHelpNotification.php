<?php

namespace App\Notifications;

use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbandonedCartHelpNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Cart $cart
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Precisa de ajuda com a sua compra?')
            ->greeting('Olá ' . $notifiable->name . ',')
            ->line('Notámos que deixou alguns livros no seu carrinho.')
            ->line('Se teve alguma dificuldade no checkout, estamos aqui para ajudar.')
            ->action('Voltar ao carrinho', route('cart.index'))
            ->line('Se já concluiu a compra, pode ignorar este email.');
    }
}
