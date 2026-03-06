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
        Schema::create('livros', function (Blueprint $table) {
            $table->id();
            $table->text('isbn');
            $table->text('nome');
            $table->foreignId('editora_id')->constrained('editoras')->cascadeOnDelete();
            $table->longText('sinopse')->nullable();
            $table->text('capa_imagem')->nullable();
            $table->text('preco');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livros');
    }
};
