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
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->nullable(); // Store email for login attempts
            $table->string('ip_address'); // Store IP address
            $table->string('user_agent')->nullable(); // Store user agent for better tracking
            $table->boolean('success')->default(false); // Whether the attempt was successful
            $table->timestamp('attempted_at')->useCurrent(); // When the attempt happened
            
            // Indexes for performance
            $table->index(['email', 'attempted_at']);
            $table->index(['ip_address', 'attempted_at']);
            $table->index('attempted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};