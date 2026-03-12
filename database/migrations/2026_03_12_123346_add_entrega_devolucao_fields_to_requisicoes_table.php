<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('requisicoes', function (Blueprint $table) {
            // Estado da requisição
            $table->string('estado', 30)
                ->default('pendente_entrega')
                ->after('numero_sequencial');

            // Entrega ao cidadão
            $table->date('data_entrega_prevista')
                ->nullable()
                ->after('data_requisicao');

            $table->date('data_entrega_real')
                ->nullable()
                ->after('data_entrega_prevista');

            // Devolução do livro
            $table->date('data_devolucao_prevista')
                ->nullable()
                ->after('data_entrega_real');

            $table->date('data_devolucao_real')
                ->nullable()
                ->after('data_devolucao_prevista');

            // Índices úteis
            $table->index('estado');
            $table->index('data_devolucao_real');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisicoes', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['data_devolucao_real']);

            $table->dropColumn([
                'estado',
                'data_entrega_prevista',
                'data_entrega_real',
                'data_devolucao_prevista',
                'data_devolucao_real',
            ]);
        });
    }
};
