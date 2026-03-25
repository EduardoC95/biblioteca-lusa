<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('requisicao_id')->constrained()->cascadeOnDelete();
            $table->foreignId('livro_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->unsignedTinyInteger('rating')->nullable(); // 1 a 5, se quiseres estrelas
            $table->text('comentario');

            $table->enum('estado', ['suspenso', 'ativo', 'recusado'])->default('suspenso');
            $table->text('justificacao_recusa')->nullable();

            $table->timestamp('moderado_em')->nullable();
            $table->foreignId('moderado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique('requisicao_id'); // uma review por requisição
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
