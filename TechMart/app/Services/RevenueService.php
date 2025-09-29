<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueService
{
    /**
     * Tính tổng doanh thu từ các đơn hàng đã hoàn thành
     * Doanh thu = tổng giá trị sản phẩm (không tính VAT và phí ship)
     */
    public function getTotalRevenue($startDate = null, $endDate = null)
    {
        $query = OrderItem::select(DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'))
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->whereIn('orders.status', ['delivered', 'completed']);

        if ($startDate && $endDate) {
            $query->whereBetween('orders.order_date', [$startDate, $endDate]);
        }

        return $query->value('total_revenue') ?? 0;
    }

    /**
     * Lấy doanh thu theo ngày trong khoảng thời gian
     */
    public function getDailyRevenue($startDate, $endDate)
    {
        return OrderItem::select(
            DB::raw('DATE(orders.order_date) as date'),
            DB::raw('SUM(order_items.price * order_items.quantity) as revenue'),
            DB::raw('COUNT(DISTINCT orders.order_id) as orders_count')
        )
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->whereIn('orders.status', ['delivered', 'completed'])
            ->whereBetween('orders.order_date', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(orders.order_date)'))
            ->orderBy('date')
            ->get();
    }

    /**
     * Lấy doanh thu 7 ngày gần nhất
     */
    public function getLast7DaysRevenue()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(6);

        $dailyRevenue = $this->getDailyRevenue($startDate, $endDate);

        // Tạo mảng 7 ngày với giá trị mặc định là 0
        $result = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dayData = $dailyRevenue->firstWhere('date', $date);
            $result[] = $dayData ? (float)$dayData->revenue : 0;
        }

        return $result;
    }

    /**
     * Lấy top sản phẩm bán chạy theo doanh thu
     */
    public function getTopProductsByRevenue($startDate = null, $endDate = null, $limit = 10)
    {
        $query = OrderItem::select(
            'products.name',
            'products.image_url',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
        )
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.product_id')
            ->whereIn('orders.status', ['delivered', 'completed'])
            ->groupBy('order_items.product_id', 'products.name', 'products.image_url')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit);

        if ($startDate && $endDate) {
            $query->whereBetween('orders.order_date', [$startDate, $endDate]);
        }

        return $query->get();
    }

    /**
     * Lấy doanh thu theo danh mục
     */
    public function getRevenueByCategory($startDate = null, $endDate = null)
    {
        $query = OrderItem::select(
            'categories.category_name',
            DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'),
            DB::raw('SUM(order_items.quantity) as total_quantity')
        )
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.product_id')
            ->join('categories', 'products.category_id', '=', 'categories.category_id')
            ->whereIn('orders.status', ['delivered', 'completed'])
            ->groupBy('categories.category_id', 'categories.category_name')
            ->orderBy('total_revenue', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('orders.order_date', [$startDate, $endDate]);
        }

        return $query->get();
    }

    /**
     * Lấy top khách hàng theo doanh thu
     */
    public function getTopCustomersByRevenue($startDate = null, $endDate = null, $limit = 10)
    {
        $query = OrderItem::select(
            'users.name',
            'users.email',
            DB::raw('COUNT(DISTINCT orders.order_id) as total_orders'),
            DB::raw('SUM(order_items.price * order_items.quantity) as total_spent')
        )
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->whereIn('orders.status', ['delivered', 'completed'])
            ->groupBy('orders.user_id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit($limit);

        if ($startDate && $endDate) {
            $query->whereBetween('orders.order_date', [$startDate, $endDate]);
        }

        return $query->get();
    }

    /**
     * Lấy thống kê đơn hàng theo trạng thái
     */
    public function getOrderStatsByStatus($startDate = null, $endDate = null)
    {
        $query = Order::select(
            'status',
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total_amount) as total_amount')
        )
            ->groupBy('status');

        if ($startDate && $endDate) {
            $query->whereBetween('order_date', [$startDate, $endDate]);
        }

        return $query->get()->keyBy('status');
    }

    /**
     * Tính giá trị đơn hàng trung bình
     */
    public function getAverageOrderValue($startDate = null, $endDate = null)
    {
        $query = OrderItem::select(DB::raw('AVG(order_items.price * order_items.quantity) as avg_value'))
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->whereIn('orders.status', ['delivered', 'completed']);

        if ($startDate && $endDate) {
            $query->whereBetween('orders.order_date', [$startDate, $endDate]);
        }

        return $query->value('avg_value') ?? 0;
    }

    /**
     * So sánh doanh thu với kỳ trước
     */
    public function getRevenueGrowth($startDate, $endDate)
    {
        $currentRevenue = $this->getTotalRevenue($startDate, $endDate);
        
        $duration = $startDate->diffInDays($endDate);
        $previousStart = $startDate->copy()->subDays($duration + 1);
        $previousEnd = $startDate->copy()->subDay();
        
        $previousRevenue = $this->getTotalRevenue($previousStart, $previousEnd);

        if ($previousRevenue > 0) {
            return (($currentRevenue - $previousRevenue) / $previousRevenue) * 100;
        }

        return 0;
    }
}
