<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Calculate total sales revenue from completed orders
        $totalSalesRevenue = Order::whereIn('status', ['processing', 'shipped', 'delivered'])
            ->sum('grand_total');

        // Convert from cents to dollars for display
        $totalSalesRevenue = $totalSalesRevenue / 100;

        // Get low stock products (stock <= 10 and active)
        $lowStockProducts = Product::where('stock_quantity', '<=', 10)
            ->where('is_active', true)
            ->orderBy('stock_quantity', 'asc')
            ->get();

        // Get recent orders to display on dashboard
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('totalSalesRevenue', 'lowStockProducts', 'recentOrders'));
    }
}
