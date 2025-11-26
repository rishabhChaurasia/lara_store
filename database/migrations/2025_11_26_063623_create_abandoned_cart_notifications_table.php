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
        Schema::create('abandoned_cart_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained('carts')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('notification_type', ['first_reminder', 'second_reminder', 'third_reminder']);
            $table->timestamp('sent_at');
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->boolean('converted')->default(false);
            $table->timestamps();

            // Add indexes for performance optimization
            $table->index('cart_id');
            $table->index('user_id');
            $table->index('notification_type');
            $table->index('sent_at');
            $table->index(['notification_type', 'sent_at']);
            $table->index('converted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abandoned_cart_notifications');
    }
};
