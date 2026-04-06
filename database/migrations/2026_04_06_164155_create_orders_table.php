<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('status')->default('pending_payment');
            $table->string('stripe_checkout_session_id')->nullable()->unique();
            $table->string('stripe_payment_intent_id')->nullable()->unique();

            $table->string('delivery_name');
            $table->string('delivery_email');
            $table->string('delivery_phone')->nullable();
            $table->string('delivery_address');
            $table->string('delivery_postal_code');
            $table->string('delivery_city');

            $table->unsignedBigInteger('total_amount');
            $table->string('currency')->default('eur');

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
