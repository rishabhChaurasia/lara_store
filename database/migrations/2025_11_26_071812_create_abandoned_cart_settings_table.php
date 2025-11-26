<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('abandoned_cart_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->text('value');
            $table->timestamps();

            $table->unique('key');
        });

        // Insert default values
        DB::table('abandoned_cart_settings')->insert([
            [
                'key' => 'first_reminder_hours',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'second_reminder_hours',
                'value' => '24',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'third_reminder_days',
                'value' => '3',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'abandoned_threshold_minutes',
                'value' => '60',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'second_reminder_discount',
                'value' => '5',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'third_reminder_discount',
                'value' => '10',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abandoned_cart_settings');
    }
};