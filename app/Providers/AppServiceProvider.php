<?php

namespace App\Providers;

use App\Support\ActivityLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            ActivityLogger::log(
                userId: $event->user->id,
                module: 'auth',
                objectId: $event->user->id,
                action: 'login',
                description: 'Login realizado com sucesso',
                request: request()
            );
        });

        Event::listen(Logout::class, function (Logout $event): void {
            ActivityLogger::log(
                userId: null,
                module: 'auth',
                objectId: $event->user?->id,
                action: 'logout',
                description: 'Logout realizado com sucesso',
                request: request()
            );
        });

        Event::listen(Registered::class, function (Registered $event): void {
            ActivityLogger::log(
                userId: $event->user->id,
                module: 'auth',
                objectId: $event->user->id,
                action: 'register',
                description: 'Novo utilizador registado: ' . $event->user->email,
                request: request()
            );
        });

        Event::listen(Failed::class, function (Failed $event): void {
            ActivityLogger::log(
                userId: null,
                module: 'auth',
                objectId: null,
                action: 'login_failed',
                description: 'Tentativa de login falhada para email: ' . ($event->credentials['email'] ?? 'desconhecido'),
                request: request()
            );
        });
    }
}
