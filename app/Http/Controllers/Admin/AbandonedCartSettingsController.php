<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AbandonedCartConfigService;
use Illuminate\Http\Request;

class AbandonedCartSettingsController extends Controller
{
    public function index()
    {
        $settings = AbandonedCartConfigService::getAllSettings();

        // Get email template settings
        $emailTemplates = [
            'first_reminder_subject' => AbandonedCartConfigService::get('first_reminder_subject', 'Your cart is waiting for you!'),
            'first_reminder_body' => AbandonedCartConfigService::get('first_reminder_body', 'We noticed you left some items in your cart. Don\'t forget to complete your purchase.'),
            'second_reminder_subject' => AbandonedCartConfigService::get('second_reminder_subject', 'Your items are still waiting - Complete your purchase!'),
            'second_reminder_body' => AbandonedCartConfigService::get('second_reminder_body', 'Your shopping cart still has items waiting for you. Don\'t miss out on these products.'),
            'third_reminder_subject' => AbandonedCartConfigService::get('third_reminder_subject', 'Last chance to complete your purchase!'),
            'third_reminder_body' => AbandonedCartConfigService::get('third_reminder_body', 'This is your final reminder about the items in your shopping cart.'),
        ];

        return view('admin.abandoned-carts.settings', compact('settings', 'emailTemplates'));
    }

    public function update(Request $request)
    {
        // Validate different form submissions
        if ($request->has('first_reminder_hours')) {
            // Update interval settings
            $validated = $request->validate([
                'first_reminder_hours' => 'required|integer|min:1',
                'second_reminder_hours' => 'required|integer|min:1',
                'third_reminder_days' => 'required|integer|min:1',
                'abandoned_threshold_minutes' => 'required|integer|min:1',
                'second_reminder_discount' => 'required|integer|min:0|max:100',
                'third_reminder_discount' => 'required|integer|min:0|max:100',
            ]);

            // Save settings
            foreach ($validated as $key => $value) {
                AbandonedCartConfigService::set($key, $value);
            }

            return redirect()->route('admin.abandoned-carts.settings.index')
                             ->with('success', 'Abandoned cart settings updated successfully!');
        } elseif ($request->has('first_reminder_subject')) {
            // Update email template settings
            $validated = $request->validate([
                'first_reminder_subject' => 'required|string|max:255',
                'first_reminder_body' => 'required|string',
                'second_reminder_subject' => 'required|string|max:255',
                'second_reminder_body' => 'required|string',
                'third_reminder_subject' => 'required|string|max:255',
                'third_reminder_body' => 'required|string',
            ]);

            // Save email template settings
            foreach ($validated as $key => $value) {
                AbandonedCartConfigService::set($key, $value);
            }

            return redirect()->route('admin.abandoned-carts.settings.index')
                             ->with('success', 'Email templates updated successfully!');
        }
    }
}
