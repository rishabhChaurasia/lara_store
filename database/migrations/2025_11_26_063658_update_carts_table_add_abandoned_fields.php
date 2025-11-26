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
        Schema::table('carts', function (Blueprint $table) {
            $table->timestamp('abandoned_at')->nullable();
            $table->timestamp('remind_at')->nullable();
            $table->integer('notified_count')->default(0);

            // Add indexes for performance optimization
            $table->index('abandoned_at');
            $table->index('remind_at');
            $table->index(['abandoned_at', 'remind_at']);
            $table->index('notified_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn(['abandoned_at', 'remind_at', 'notified_count']);
        });
    }
};
