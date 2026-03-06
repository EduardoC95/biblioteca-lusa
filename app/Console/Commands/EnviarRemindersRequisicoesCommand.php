<?php

namespace App\Console\Commands;

use App\Mail\RequisicaoReminderMail;
use App\Models\Requisicao;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarRemindersRequisicoesCommand extends Command
{
    protected $signature = 'requisicoes:enviar-reminders';

    protected $description = 'Envia lembretes de entrega para requisições com devolução prevista para amanhã.';

    public function handle(): int
    {
        $amanha = CarbonImmutable::tomorrow();

        $requisicoes = Requisicao::query()
            ->ativas()
            ->whereDate('data_prevista_entrega', $amanha)
            ->whereNull('reminder_enviado_em')
            ->get();

        foreach ($requisicoes as $requisicao) {
            Mail::to($requisicao->cidadao_email)->send(new RequisicaoReminderMail($requisicao));

            $requisicao->update([
                'reminder_enviado_em' => now(),
            ]);
        }

        $this->info('Reminders enviados: ' . $requisicoes->count());

        return self::SUCCESS;
    }
}
