<?php

namespace App\Console\Commands;

use App\Models\Cart;
use App\Notifications\AbandonedCartHelpNotification;
use App\Support\ActivityLogger;
use Illuminate\Console\Command;

class SendCartHelpEmails extends Command
{
    protected $signature = 'carts:send-help-emails';

    protected $description = 'Envia emails para carrinhos abandonados há mais de 1 hora';

    public function handle(): int
    {
        $carts = Cart::query()
            ->with(['user', 'items.livro'])
            ->whereNotNull('last_activity_at')
            ->whereNull('help_email_sent_at')
            ->whereNull('converted_at')
            ->where('last_activity_at', '<=', now()->subHour())
            ->whereHas('items')
            ->get();

        foreach ($carts as $cart) {
            if (! $cart->user) {
                continue;
            }

            $cart->user->notify(new AbandonedCartHelpNotification($cart));

            $cart->update([
                'help_email_sent_at' => now(),
            ]);

            ActivityLogger::log(
                userId: $cart->user->id,
                module: 'cart',
                objectId: $cart->id,
                action: 'help_email_sent',
                description: 'Email de ajuda enviado por carrinho abandonado',
                request: null
            );

            $this->info("Email enviado para user {$cart->user->id}");
        }

        return self::SUCCESS;
    }
}
