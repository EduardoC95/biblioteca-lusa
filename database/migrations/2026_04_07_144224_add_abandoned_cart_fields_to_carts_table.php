<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->timestamp('last_activity_at')->nullable()->after('user_id');
            $table->timestamp('abandoned_email_sent_at')->nullable()->after('last_activity_at');
            $table->timestamp('converted_at')->nullable()->after('abandoned_email_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn([
                'last_activity_at',
                'abandoned_email_sent_at',
                'converted_at',
            ]);
        });
    }
};
