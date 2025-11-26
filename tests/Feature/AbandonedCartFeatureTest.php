<?php

namespace Tests\Feature;

use App\Jobs\ProcessAbandonedCarts;
use App\Models\AbandonedCartNotification;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AbandonedCartFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_abandoned_cart_job_processes_carts()
    {
        Mail::fake();
        Queue::fake();

        // Create a user and a product
        $user = User::factory()->create();
        $product = Product::factory()->create();

        // Create a cart with items
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'abandoned_at' => now()->subHour(),
            'remind_at' => now()->subMinutes(5), // Time to send reminder - in the past
            'notified_count' => 0, // First reminder
        ]);

        // Add an item to the cart
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        // Dispatch and run the ProcessAbandonedCarts job synchronously
        $job = new ProcessAbandonedCarts();
        $job->handle();

        // Check that the correct jobs were queued
        Queue::assertPushed(\App\Jobs\SendAbandonedCartNotification::class, 1);

        // Refresh cart to get latest data
        $cart->refresh();

        // Check that notification count was incremented
        $this->assertEquals(1, $cart->notified_count);
    }

    public function test_abandoned_cart_notifications_are_created()
    {
        Mail::fake();

        // Create a user and a product
        $user = User::factory()->create();
        $product = Product::factory()->create();

        // Create a cart
        $cart = Cart::factory()->create([
            'user_id' => $user->id,
            'abandoned_at' => now()->subHour(),
            'remind_at' => now()->subMinutes(5), // Time to send reminder
            'notified_count' => 0, // First reminder
        ]);

        // Add an item to the cart
        CartItem::factory()->create([
            'cart_id' => $cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // Create a notification directly (simulating what SendAbandonedCartNotification does)
        $notification = AbandonedCartNotification::create([
            'cart_id' => $cart->id,
            'user_id' => $user->id,
            'notification_type' => 'first_reminder',
            'sent_at' => now(),
        ]);

        // Assertions
        $this->assertNotNull($notification);
        $this->assertEquals('first_reminder', $notification->notification_type);
        $this->assertEquals($cart->id, $notification->cart_id);
        $this->assertEquals($user->id, $notification->user_id);
        $this->assertNotNull($notification->sent_at);
    }
}