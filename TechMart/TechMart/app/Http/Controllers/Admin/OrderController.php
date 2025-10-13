<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderItems.product', 'orderItems.productVariant']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $orders = $query->orderBy('order_date', 'desc')->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order
     */
    public function show($id)
    {
        // Xác định primary key của Order model
        $orderModel = new Order();
        $primaryKey = $orderModel->getKeyName();

        // Load order với tất cả relationships cần thiết
        $order = Order::with([
            'user',
            'orderItems' => function ($query) {
                $query->with(['product', 'productVariant']);
            }
        ])->where($primaryKey, $id)->firstOrFail();

        // Debug: Log để kiểm tra dữ liệu
        Log::info('Order details', [
            'order_id' => $order->id ?? $order->order_id,
            'status' => $order->status,
            'items_count' => $order->orderItems->count(),
        ]);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        Log::info('Update status request', [
            'order_id' => $id,
            'new_status' => $request->status,
            'request_data' => $request->all()
        ]);

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,completed,cancelled'
        ]);

        try {
            $orderModel = new Order();
            $primaryKey = $orderModel->getKeyName();

            $order = Order::where($primaryKey, $id)->firstOrFail();

            $oldStatus = $order->status;
            $newStatus = $request->status;

            // Cập nhật trạng thái
            $order->status = $newStatus;

            // Cập nhật timestamp tương ứng
            if ($newStatus === 'shipped') {
                if (Schema::hasColumn('orders', 'shipped_at')) {
                    $order->shipped_at = now();
                }
            } elseif ($newStatus === 'delivered') {
                if (Schema::hasColumn('orders', 'delivered_at')) {
                    $order->delivered_at = now();
                }
                if (Schema::hasColumn('orders', 'payment_status')) {
                    $order->payment_status = 'paid';
                }
            } elseif ($newStatus === 'completed') {
                // Đảm bảo đơn hàng hoàn thành có payment_status là paid
                if (Schema::hasColumn('orders', 'payment_status')) {
                    $order->payment_status = 'paid';
                }
                if (Schema::hasColumn('orders', 'delivered_at') && !$order->delivered_at) {
                    $order->delivered_at = now();
                }
            }

            $saved = $order->save();

            Log::info('Order status update result', [
                'order_id' => $id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'saved' => $saved,
                'updated_order' => $order->toArray()
            ]);

            if ($saved) {
                return redirect()->back()->with('success', "Trạng thái đơn hàng đã được cập nhật từ '{$oldStatus}' thành '{$newStatus}'");
            } else {
                return redirect()->back()->with('error', 'Không thể cập nhật trạng thái đơn hàng');
            }
        } catch (\Exception $e) {
            Log::error('Error updating order status', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi cập nhật trạng thái: ' . $e->getMessage());
        }
    }

    /**
     * Delete order (soft delete)
     */
    public function destroy($id)
    {
        try {
            $orderModel = new Order();
            $primaryKey = $orderModel->getKeyName();

            $order = Order::where($primaryKey, $id)->firstOrFail();

            // Chỉ cho phép xóa đơn hàng đã hủy
            if ($order->status !== 'cancelled') {
                return redirect()->back()->with('error', 'Chỉ có thể xóa đơn hàng đã hủy');
            }

            $order->delete();

            return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng đã được xóa');
        } catch (\Exception $e) {
            Log::error('Error deleting order', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi xóa đơn hàng: ' . $e->getMessage());
        }
    }
}
