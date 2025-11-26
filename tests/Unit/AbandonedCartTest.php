<?php

namespace Tests\Unit;

use App\Models\AbandonedCartNotification;
use App\Models\Cart;
use App\Models\User;
use App\Services\AbandonedCartConfigService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

class AbandonedCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_can_be_marked_as_abandoned()
    {
        // Create a test cart
        $cart = Cart::factory()->create();

        // Mark the cart as abandoned
        $cart->markAsAbandoned();

        // Refresh the cart from the database
        $cart->refresh();

        // Assertions
        $this->assertNotNull($cart->abandoned_at);
        $this->assertNotNull($cart->remind_at);
        
        // Check that remind_at is approximately 1 hour from now (based on config)
        $expectedRemindTime = now()->addHours(
            AbandonedCartConfigService::get('first_reminder_hours', 1)
        );
        $this->assertEqualsWithDelta($expectedRemindTime->timestamp, $cart->remind_at->timestamp, 60); // 1 minute tolerance
    }

    public function test_is_abandoned_method_works()
    {
        // Create a cart and mark it as abandoned
        $cart = Cart::factory()->create();
        $cart->markAsAbandoned();

        // Check that it's considered abandoned
        $this->assertTrue($cart->isAbandoned());

        // Create a cart without marking it as abandoned
        $cart2 = Cart::factory()->create();

        // This should not be abandoned yet if threshold hasn't passed
        $this->assertFalse($cart2->isAbandoned());
    }

    public function test_is_time_for_reminder_method()
    {
        // Create a cart with remind_at in the past
        $cart = Cart::factory()->create([
            'remind_at' => now()->subHour(),
            'abandoned_at' => now()->subHours(2),
        ]);

        $this->assertTrue($cart->isTimeForReminder());

        // Create a cart with remind_at in the future
        $cart2 = Cart::factory()->create([
            'remind_at' => now()->addHour(),
            'abandoned_at' => now()->subHour(),
        ]);

        $this->assertFalse($cart2->isTimeForReminder());
    }

    public function test_next_notification_type()
    {
        $cart = Cart::factory()->create(['notified_count' => 0]);
        $this->assertEquals('first_reminder', $cart->getNextNotificationType());

        $cart->notified_count = 1;
        $this->assertEquals('second_reminder', $cart->getNextNotificationType());

        $cart->notified_count = 2;
        $this->assertEquals('third_reminder', $cart->getNextNotificationType());

        $cart->notified_count = 3;
        $this->assertNull($cart->getNextNotificationType());
    }

    public function test_schedule_next_reminder()
    {
        $cart = Cart::factory()->create([
            'notified_count' => 0, // After first reminder
        ]);

        $cart->scheduleNextReminder();

        $cart->refresh();

        // Should be scheduled for 24 hours later (default config)
        $expectedRemindTime = now()->addHours(
            AbandonedCartConfigService::get('second_reminder_hours', 24)
        );
        $this->assertEqualsWithDelta($expectedRemindTime->timestamp, $cart->remind_at->timestamp, 60);
    }

    public function test_increment_notification_count()
    {
        $cart = Cart::factory()->create(['notified_count' => 1]);
        
        $cart->incrementNotificationCount();

        $cart->refresh();

        $this->assertEquals(2, $cart->notified_count);
    }
}