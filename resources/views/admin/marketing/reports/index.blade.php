@extends('layouts.admin')

@section('title', 'Marketing Reports')
@section('header', 'Marketing Reports')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Revenue</h3>
            <p class="text-3xl font-bold text-indigo-600">$0.00</p>
            <p class="text-sm text-gray-500 mt-1">Last 30 days</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Orders</h3>
            <p class="text-3xl font-bold text-indigo-600">0</p>
            <p class="text-sm text-gray-500 mt-1">Last 30 days</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Conversion Rate</h3>
            <p class="text-3xl font-bold text-indigo-600">0.0%</p>
            <p class="text-sm text-gray-500 mt-1">Last 30 days</p>
        </div>
    </div>

    <!-- Sales Chart Controls -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 md:mb-0">Sales Report</h2>
            <div class="flex flex-wrap gap-3">
                <select id="report-period" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="daily">Daily</option>
                    <option value="monthly" selected>Monthly</option>
                </select>
                <input type="date" id="start-date" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                <input type="date" id="end-date" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                <button id="apply-filters" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Apply
                </button>
            </div>
        </div>

        <!-- Chart placeholder -->
        <div class="h-80 bg-gray-50 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
            <canvas id="salesChart" class="w-full h-full"></canvas>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Quick Access</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.marketing.reports.stock') }}" class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white p-6 rounded-lg shadow hover:from-yellow-600 hover:to-yellow-700 transition duration-150 ease-in-out">
                <h3 class="text-lg font-semibold mb-2">Stock Management</h3>
                <p class="text-sm opacity-80">Monitor inventory levels and low stock alerts</p>
            </a>
            <div class="bg-gray-50 p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Sales Overview</h3>
                <p class="text-sm text-gray-600">Review sales performance and trends</p>
            </div>
            <div class="bg-gray-50 p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Marketing Analytics</h3>
                <p class="text-sm text-gray-600">Analyze marketing campaign effectiveness</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Selling Products -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Top Selling Products</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="top-selling-table-body">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Loading...</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">0</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">$0.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Coupons Usage -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Coupon Usage</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coupon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">WELCOME10</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">10% off</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">23 / 50</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">SUMMER20</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$20 off</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">15 / 30</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">FREESHIP</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Free shipping</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">41 / 100</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">EXPIRED50</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$50 off</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">8 / 10</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Expired
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js for the sales chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let salesChart;
        let currentPeriod = 'monthly';
        let startDate = '';
        let endDate = '';

        // Function to fetch sales data
        function fetchSalesData() {
            // Show loading state for totals
            document.querySelector('.bg-white.p-6.rounded-lg.shadow:first-child p.text-3xl').textContent = 'Loading...';
            document.querySelector('.bg-white.p-6.rounded-lg.shadow:nth-child(2) p.text-3xl').textContent = 'Loading...';

            fetch(`/admin/marketing/reports/sales-data?period=${currentPeriod}&start_date=${startDate}&end_date=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    updateTotals(data);
                    updateSalesChart(data);
                    updateTopSellingProducts(data);
                })
                .catch(error => {
                    console.error('Error fetching sales data:', error);
                });
        }

        // Function to update totals
        async function updateTotals(data) {
            // Calculate total revenue from sales data
            let totalRevenue = 0;
            data.sales_data.forEach(item => {
                totalRevenue += item.total || 0;
            });

            // Count orders based on sales data
            let orderCount = data.sales_data.length;

            // Update total revenue
            document.querySelector('.bg-white.p-6.rounded-lg.shadow:first-child p.text-3xl').textContent = '$' + totalRevenue.toFixed(2);

            // Update order count
            document.querySelector('.bg-white.p-6.rounded-lg.shadow:nth-child(2) p.text-3xl').textContent = orderCount;

            // Calculate and update conversion rate
            try {
                const response = await fetch('/admin/marketing/reports/conversion-rate');
                if (response.ok) {
                    const conversionData = await response.json();
                    document.querySelector('.bg-white.p-6.rounded-lg.shadow:nth-child(3) p.text-3xl').textContent = conversionData.conversion_rate + '%';
                } else {
                    document.querySelector('.bg-white.p-6.rounded-lg.shadow:nth-child(3) p.text-3xl').textContent = '0.0%';
                }
            } catch (error) {
                document.querySelector('.bg-white.p-6.rounded-lg.shadow:nth-child(3) p.text-3xl').textContent = '0.0%';
            }
        }

        // Function to update sales chart
        function updateSalesChart(data) {
            const ctx = document.getElementById('salesChart').getContext('2d');

            // Destroy existing chart if it exists
            if (salesChart) {
                salesChart.destroy();
            }

            let labels = [];
            let values = [];

            if (currentPeriod === 'daily') {
                data.sales_data.forEach(item => {
                    labels.push(item.date);
                    values.push(item.total);
                });
            } else {
                data.sales_data.forEach(item => {
                    labels.push(item.month);
                    values.push(item.total);
                });
            }

            salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: currentPeriod === 'daily' ? 'Daily Sales' : 'Monthly Sales',
                        data: values,
                        borderColor: 'rgb(99, 102, 241)',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: currentPeriod === 'daily' ? 'Daily Sales Trend' : 'Monthly Sales Trend'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Function to update top selling products
        function updateTopSellingProducts(data) {
            const tbody = document.getElementById('top-selling-table-body');
            tbody.innerHTML = '';

            if (data.top_products.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500" colspan="3">
                            No top selling products data available
                        </td>
                    </tr>
                `;
                return;
            }

            data.top_products.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${product.name}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${product.quantity}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">$${product.revenue.toFixed(2)}</td>
                `;
                tbody.appendChild(row);
            });
        }

        // Event listeners for filters
        document.getElementById('report-period').addEventListener('change', function() {
            currentPeriod = this.value;
            fetchSalesData();
        });

        document.getElementById('apply-filters').addEventListener('click', function() {
            startDate = document.getElementById('start-date').value;
            endDate = document.getElementById('end-date').value;
            fetchSalesData();
        });

        // Initialize the charts with default data
        fetchSalesData();
    });
</script>
@endsection