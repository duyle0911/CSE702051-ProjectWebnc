<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\RevenueService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RevenueController extends Controller
{
    protected $revenueService;

    public function __construct(RevenueService $revenueService)
    {
        $this->middleware(['auth', 'admin']);
        $this->revenueService = $revenueService;
    }

    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Xác định khoảng thời gian
        $dateRange = $this->getDateRange($period, $startDate, $endDate);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Thống kê tổng quan
        $overview = [
            'current_revenue' => $this->revenueService->getTotalRevenue($startDate, $endDate),
            'total_orders' => \App\Models\Order::whereBetween('order_date', [$startDate, $endDate])->count(),
            'completed_orders' => \App\Models\Order::whereBetween('order_date', [$startDate, $endDate])
                ->whereIn('status', ['delivered', 'completed'])->count(),
            'avg_order_value' => $this->revenueService->getAverageOrderValue($startDate, $endDate),
            'revenue_growth' => $this->revenueService->getRevenueGrowth($startDate, $endDate),
        ];

        $overview['completion_rate'] = $overview['total_orders'] > 0
            ? ($overview['completed_orders'] / $overview['total_orders']) * 100
            : 0;

        // Biểu đồ doanh thu theo ngày
        $dailyRevenue = $this->revenueService->getDailyRevenue($startDate, $endDate);
        $revenueChart = $this->formatRevenueChart($dailyRevenue, $startDate, $endDate);

        // Top sản phẩm bán chạy
        $topProducts = $this->revenueService->getTopProductsByRevenue($startDate, $endDate);

        // Doanh thu theo danh mục
        $categoryRevenue = $this->revenueService->getRevenueByCategory($startDate, $endDate);

        // Thống kê đơn hàng theo trạng thái
        $orderStats = $this->revenueService->getOrderStatsByStatus($startDate, $endDate);

        // Top khách hàng
        $topCustomers = $this->revenueService->getTopCustomersByRevenue($startDate, $endDate);

        return view('admin.revenue.index', compact(
            'overview',
            'revenueChart',
            'topProducts',
            'categoryRevenue',
            'orderStats',
            'topCustomers',
            'period',
            'startDate',
            'endDate'
        ));
    }

    private function getDateRange($period, $startDate = null, $endDate = null)
    {
        if ($startDate && $endDate) {
            return [
                'start' => Carbon::parse($startDate)->startOfDay(),
                'end' => Carbon::parse($endDate)->endOfDay()
            ];
        }

        $now = Carbon::now();

        switch ($period) {
            case 'day':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];
            case 'week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
            case 'month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
            case 'year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear()
                ];
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
        }
    }

    private function formatRevenueChart($dailyRevenue, $startDate, $endDate)
    {
        $labels = [];
        $revenues = [];
        $orders = [];

        // Tạo tất cả các ngày trong khoảng thời gian
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d/m');

            $dayData = $dailyRevenue->firstWhere('date', $dateStr);
            $revenues[] = $dayData ? (float)$dayData->revenue : 0;
            $orders[] = $dayData ? (int)$dayData->orders_count : 0;

            $current->addDay();
        }

        return [
            'labels' => $labels,
            'revenues' => $revenues,
            'orders' => $orders
        ];
    }

    public function export(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $dateRange = $this->getDateRange($period, $startDate, $endDate);

        // Lấy dữ liệu chi tiết
        $orders = \App\Models\Order::with(['user', 'orderItems.product'])
            ->whereBetween('order_date', [$dateRange['start'], $dateRange['end']])
            ->whereIn('status', ['delivered', 'completed'])
            ->orderBy('order_date', 'desc')
            ->get();

        $filename = 'revenue_report_' . $dateRange['start']->format('Y-m-d') . '_to_' . $dateRange['end']->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'Mã đơn hàng',
                'Ngày đặt',
                'Khách hàng',
                'Email',
                'Doanh thu sản phẩm',
                'Trạng thái'
            ]);

            // Data
            foreach ($orders as $order) {
                $productRevenue = $order->orderItems->sum(function ($item) {
                    return $item->price * $item->quantity;
                });

                fputcsv($file, [
                    $order->order_number ?? '#' . $order->order_id,
                    $order->order_date->format('d/m/Y H:i'),
                    $order->user->name ?? 'N/A',
                    $order->user->email ?? 'N/A',
                    number_format($productRevenue, 0, ',', '.') . '₫',
                    $order->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
