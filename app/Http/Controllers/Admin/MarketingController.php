<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MarketingController extends Controller
{
    public function couponsIndex()
    {
        $coupons = Coupon::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.marketing.coupons.index', compact('coupons'));
    }

    public function couponsCreate()
    {
        return view('admin.marketing.coupons.create');
    }

    public function couponsStore(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        Coupon::create($validated);

        return redirect()->route('admin.marketing.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function couponsEdit(Coupon $coupon)
    {
        return view('admin.marketing.coupons.edit', compact('coupon'));
    }

    public function couponsUpdate(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|' . Rule::unique('coupons', 'code')->ignore($coupon->id),
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $coupon->update($validated);

        return redirect()->route('admin.marketing.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function couponsDestroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.marketing.coupons.index')->with('success', 'Coupon deleted successfully.');
    }

    public function reportsIndex()
    {
        return view('admin.marketing.reports.index');
    }

    public function inventoryIndex()
    {
        $products = \App\Models\Product::orderBy('created_at', 'desc')->paginate(15);
        $categories = \App\Models\Category::all();

        return view('admin.marketing.inventory.index', compact('products', 'categories'));
    }

    public function bulkUpdateStock(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'stock_changes' => 'required|array',
        ]);

        $newStock = $request->stock_changes['template'] ?? null;

        if ($newStock !== null && $newStock >= 0) {
            \App\Models\Product::whereIn('id', $request->product_ids)->update(['stock_quantity' => $newStock]);
        }

        return redirect()->back()->with('success', 'Stock levels updated successfully for selected products.');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'status' => 'required|in:active,inactive',
        ]);

        $status = $request->status === 'active';

        \App\Models\Product::whereIn('id', $request->product_ids)->update(['is_active' => $status]);

        return redirect()->back()->with('success', 'Product statuses updated successfully.');
    }

    public function exportProducts()
    {
        $products = \App\Models\Product::with('category')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory_' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID', 'Name', 'Description', 'Price', 'Stock Quantity',
                'SKU', 'Category', 'Status', 'Created At'
            ]);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->description,
                    $product->price / 100, // Convert from cents to dollars
                    $product->stock_quantity,
                    $product->sku,
                    $product->category->name ?? 'N/A',
                    $product->is_active ? 'Active' : 'Inactive',
                    $product->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, 'inventory_' . now()->format('Y-m-d') . '.csv', $headers);
    }

    public function importProducts(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));

        // Remove header row
        $header = array_shift($data);

        // Validate headers
        $requiredHeaders = ['ID', 'Name', 'Description', 'Price', 'Stock Quantity', 'SKU', 'Category', 'Status'];
        if (!empty(array_diff($requiredHeaders, $header))) {
            return redirect()->back()->with('error', 'CSV file has incorrect format.');
        }

        $processed = 0;
        $errors = [];

        foreach ($data as $row) {
            if (count($row) < count($requiredHeaders)) {
                continue; // Skip incomplete rows
            }

            $row = array_pad($row, count($requiredHeaders), ''); // Pad short rows with empty strings

            $id = $row[0];
            $name = $row[1];
            $description = $row[2];
            $price = floatval($row[3]) * 100; // Convert to cents
            $stockQuantity = intval($row[4]);
            $sku = $row[5];
            $categoryName = $row[6];
            $isActive = strtolower($row[7]) === 'active';

            if (empty($name) || empty($sku)) {
                continue; // Skip rows without required fields
            }

            try {
                // Find or create category
                $category = \App\Models\Category::firstOrCreate(
                    ['name' => $categoryName],
                    ['name' => $categoryName, 'is_active' => true]
                );

                // Create or update product
                $product = \App\Models\Product::updateOrCreate(
                    ['id' => $id],
                    [
                        'name' => $name,
                        'description' => $description,
                        'price' => $price,
                        'stock_quantity' => $stockQuantity,
                        'sku' => $sku,
                        'category_id' => $category->id,
                        'is_active' => $isActive,
                    ]
                );

                $processed++;
            } catch (\Exception $e) {
                $errors[] = "Error processing product: " . $name . " (" . $e->getMessage() . ")";
            }
        }

        $message = "Successfully processed $processed products.";
        if (!empty($errors)) {
            $message .= " " . count($errors) . " errors occurred.";
        }

        return redirect()->back()->with('success', $message);
    }

    public function stockReport()
    {
        // Get products with low stock
        $lowStockProducts = \App\Models\Product::where('stock_quantity', '<=', 10)
            ->where('is_active', true)
            ->orderBy('stock_quantity', 'asc')
            ->get();

        // Get out of stock products
        $outOfStockProducts = \App\Models\Product::where('stock_quantity', 0)
            ->where('is_active', true)
            ->get();

        // Get inventory value
        $totalInventoryValue = \App\Models\Product::where('is_active', true)
            ->sum(\DB::raw('price * stock_quantity'));

        return view('admin.marketing.reports.stock', compact(
            'lowStockProducts',
            'outOfStockProducts',
            'totalInventoryValue'
        ));
    }

    public function stockReportData(Request $request)
    {
        // Get stock data for reporting
        $period = $request->get('period', 'monthly');

        // For this example, we'll return mock data
        $stockData = [
            ['date' => now()->format('Y-m-d'), 'quantity' => 500],
            ['date' => now()->subDay()->format('Y-m-d'), 'quantity' => 520],
            ['date' => now()->subDays(2)->format('Y-m-d'), 'quantity' => 480],
        ];

        $lowStockAlerts = \App\Models\Product::where('stock_quantity', '<=', 5)
            ->where('is_active', true)
            ->select('name', 'stock_quantity', 'sku')
            ->get();

        return response()->json([
            'stock_data' => $stockData,
            'low_stock_alerts' => $lowStockAlerts,
        ]);
    }

    public function salesReportData(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $query = \App\Models\Order::query();

        // Filter by date range if provided
        if ($start_date && $end_date) {
            $query->whereBetween('created_at', [$start_date, $end_date]);
        } elseif ($start_date) {
            $query->whereDate('created_at', '>=', $start_date);
        } elseif ($end_date) {
            $query->whereDate('created_at', '<=', $end_date);
        }

        // Filter by status (only completed orders count as sales)
        $query->whereIn('status', ['processing', 'shipped', 'delivered']);

        if ($period === 'daily') {
            // Group by day for daily report
            $salesData = $query
                ->selectRaw('DATE(created_at) as date, SUM(grand_total) as total')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30) // Last 30 days
                ->get()
                ->map(function ($item) {
                    return [
                        'date' => $item->date,
                        'total' => $item->total / 100 // Convert from cents to dollars
                    ];
                })
                ->values()
                ->toArray();
        } else {
            // Monthly report
            $salesData = $query
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(grand_total) as total')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(12) // Last 12 months
                ->get()
                ->map(function ($item) {
                    return [
                        'month' => $item->month,
                        'total' => $item->total / 100 // Convert from cents to dollars
                    ];
                })
                ->values()
                ->toArray();
        }

        // Get top selling products
        $orderItems = \DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', \DB::raw('SUM(order_items.quantity) as total_quantity'), \DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue'))
            ->where('orders.status', 'delivered') // Only count delivered orders
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->limit(10) // Top 10 selling products
            ->get();

        $topProducts = $orderItems->map(function ($item) {
            return [
                'name' => $item->name,
                'quantity' => $item->total_quantity,
                'revenue' => $item->total_revenue / 100 // Convert from cents to dollars
            ];
        })->values()->toArray();

        return response()->json([
            'sales_data' => $salesData,
            'top_products' => $topProducts,
        ]);
    }

    public function conversionRate()
    {
        // Calculate conversion rate: (completed orders / total visitors) * 100
        // Since we don't track visitors in this basic implementation, I'll calculate
        // the ratio of completed orders to total orders as a proxy

        $totalOrders = \App\Models\Order::count();
        $completedOrders = \App\Models\Order::whereIn('status', ['processing', 'shipped', 'delivered'])->count();

        $conversionRate = $totalOrders > 0 ? ($completedOrders / $totalOrders) * 100 : 0;

        return response()->json([
            'conversion_rate' => number_format($conversionRate, 2)
        ]);
    }
}