<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\RevenueService;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $revenueService = new RevenueService();

        $stats = [
            'total_users' => User::where('role', 'customer')->count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_categories' => Category::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_revenue' => $revenueService->getTotalRevenue(),
        ];

        // Lấy dữ liệu doanh thu 7 ngày gần nhất
        $revenueData = $revenueService->getLast7DaysRevenue();

        $recentOrders = Order::with(['user', 'orderItems.productVariant.product'])
            ->orderBy('order_date', 'desc')
            ->limit(5)
            ->get();

        $lowStockProducts = Product::where('stock_quantity', '<', 10)
            ->orderBy('stock_quantity', 'asc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStockProducts', 'revenueData'));
    }
}
