<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requisicoes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('numero_sequencial')->unique();
            $table->foreignId('livro_id')->constrained('livros')->cascadeOnDelete();
            $table->foreignId('cidadao_id')->constrained('users')->cascadeOnDelete();
            $table->string('cidadao_nome');
            $table->string('cidadao_email');
            $table->string('cidadao_foto_path', 2048)->nullable();
            $table->date('data_requisicao');
            $table->date('data_prevista_entrega');
            $table->date('data_real_entrega')->nullable();
            $table->unsignedInteger('dias_decorridos')->nullable();
            $table->foreignId('devolucao_confirmada_por_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reminder_enviado_em')->nullable();
            $table->timestamps();

            $table->index(['livro_id', 'data_real_entrega']);
            $table->index(['cidadao_id', 'data_real_entrega']);
            $table->index('data_prevista_entrega');
            $table->index('data_real_entrega');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisicoes');
    }
};
