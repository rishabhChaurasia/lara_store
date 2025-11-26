<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCartNotification;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbandonedCartController extends Controller
{
    public function index()
    {
        // Get overall statistics
        $totalAbandonedCarts = Cart::whereNotNull('abandoned_at')->count();
        $totalNotificationsSent = AbandonedCartNotification::count();
        $recoveredCarts = AbandonedCartNotification::where('converted', true)->count();

        // Calculate conversion rate
        $conversionRate = $totalNotificationsSent > 0
            ? round(($recoveredCarts / $totalNotificationsSent) * 100, 2)
            : 0;

        // Get notification breakdown by type
        $notificationBreakdown = AbandonedCartNotification::select(
            'notification_type',
            DB::raw('COUNT(*) as count')
        )->groupBy('notification_type')->get();

        // Get recent abandoned carts
        $recentAbandonedCarts = Cart::whereNotNull('abandoned_at')
            ->with(['user', 'items.product'])
            ->orderBy('abandoned_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.abandoned-carts.index', compact(
            'totalAbandonedCarts',
            'totalNotificationsSent',
            'recoveredCarts',
            'conversionRate',
            'notificationBreakdown',
            'recentAbandonedCarts'
        ));
    }
}
