<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $query = $user->orders()
            ->with(['orderItems.product', 'orderItems.productVariant'])
            ->orderBy('order_date', 'desc');

        // Lọc theo trạng thái nếu có
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo mã đơn hàng
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(10);

        // Thống kê trạng thái đơn hàng
        $statusCounts = $user->orders()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('orders.index', compact('orders', 'statusCounts'));
    }

    public function show(Order $order)
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        // Kiểm tra quyền truy cập thủ công thay vì dùng Policy
        if ($order->user_id !== Auth::id() && !$authUser->isAdmin()) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        $order->load(['orderItems.product', 'orderItems.productVariant', 'user']);

        return view('orders.show', compact('order'));
    }

    /**
     * Cancel an order
     */
    public function cancel(Order $order)
    {
        Log::info('Cancel order request', [
            'order_id' => $order->order_id,
            'user_id' => Auth::id(),
            'order_user_id' => $order->user_id,
            'status' => $order->status
        ]);

        // Kiểm tra quyền truy cập thủ công
        if ($order->user_id !== Auth::id()) {
            Log::warning('Unauthorized cancel attempt', [
                'order_id' => $order->order_id,
                'auth_user_id' => Auth::id(),
                'order_user_id' => $order->user_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền hủy đơn hàng này'
            ], 403);
        }

        // Chỉ cho phép hủy đơn hàng ở trạng thái pending
        if ($order->status !== 'pending') {
            Log::warning('Cannot cancel order - invalid status', [
                'order_id' => $order->order_id,
                'current_status' => $order->status
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy đơn hàng này. Chỉ có thể hủy đơn hàng đang chờ xử lý.'
            ], 400);
        }

        try {
            $order->update(['status' => 'cancelled']);

            Log::info('Order cancelled successfully', [
                'order_id' => $order->order_id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đơn hàng đã được hủy thành công'
            ]);
        } catch (\Exception $e) {
            Log::error('Error cancelling order', [
                'order_id' => $order->order_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi hủy đơn hàng. Vui lòng thử lại.'
            ], 500);
        }
    }

    /**
     * Reorder - Add all items from a completed order back to cart
     */
    public function reorder(Order $order)
    {
        Log::info('Reorder request', [
            'order_id' => $order->order_id,
            'user_id' => Auth::id(),
            'order_user_id' => $order->user_id,
            'status' => $order->status
        ]);

        // Kiểm tra quyền truy cập
        if ($order->user_id !== Auth::id()) {
            Log::warning('Unauthorized reorder attempt', [
                'order_id' => $order->order_id,
                'auth_user_id' => Auth::id(),
                'order_user_id' => $order->user_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền mua lại đơn hàng này'
            ], 403);
        }

        // Chỉ cho phép mua lại đơn hàng đã hoàn thành hoặc đã giao
        if (!in_array($order->status, ['delivered', 'completed'])) {
            Log::warning('Cannot reorder - invalid status', [
                'order_id' => $order->order_id,
                'current_status' => $order->status
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể mua lại đơn hàng đã hoàn thành.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            /** @var User $user */
            $user = Auth::user();
            $addedItems = 0;
            $unavailableItems = [];
            $outOfStockItems = [];

            // Load order items với product và variant
            $order->load(['orderItems.product', 'orderItems.productVariant']);

            foreach ($order->orderItems as $orderItem) {
                // Kiểm tra sản phẩm còn tồn tại không
                if (!$orderItem->product) {
                    $unavailableItems[] = $orderItem->product_name ?? 'Sản phẩm không xác định';
                    continue;
                }

                $product = $orderItem->product;
                $variantId = $orderItem->variant_id;

                // Kiểm tra variant còn tồn tại không (nếu có)
                if ($variantId && $variantId > 1) {
                    $variant = ProductVariant::find($variantId);
                    if (!$variant) {
                        $unavailableItems[] = $product->name . ' (' . ($orderItem->variant_name ?? 'Biến thể không xác định') . ')';
                        continue;
                    }
                }

                // Kiểm tra tồn kho
                $availableStock = $product->stock_quantity;
                if ($variantId && $variantId > 1) {
                    $variant = ProductVariant::find($variantId);
                    $availableStock = $variant ? $variant->stock_quantity : 0;
                }

                if ($availableStock < $orderItem->quantity) {
                    $outOfStockItems[] = [
                        'name' => $product->name . ($orderItem->variant_name && $orderItem->variant_name !== 'Mặc định' ? ' (' . $orderItem->variant_name . ')' : ''),
                        'requested' => $orderItem->quantity,
                        'available' => $availableStock
                    ];

                    // Nếu có ít hàng hơn yêu cầu, thêm số lượng có sẵn
                    if ($availableStock > 0) {
                        $this->addToCart($user, $product, $variantId, $availableStock);
                        $addedItems++;
                    }
                    continue;
                }

                // Thêm vào giỏ hàng
                $this->addToCart($user, $product, $variantId, $orderItem->quantity);
                $addedItems++;
            }

            DB::commit();

            // Tạo thông báo kết quả
            $message = "Đã thêm {$addedItems} sản phẩm vào giỏ hàng.";

            if (!empty($unavailableItems)) {
                $message .= " Một số sản phẩm không còn bán: " . implode(', ', $unavailableItems) . ".";
            }

            if (!empty($outOfStockItems)) {
                $message .= " Một số sản phẩm không đủ số lượng: ";
                $stockMessages = [];
                foreach ($outOfStockItems as $item) {
                    $stockMessages[] = "{$item['name']} (yêu cầu: {$item['requested']}, có sẵn: {$item['available']})";
                }
                $message .= implode(', ', $stockMessages) . ".";
            }

            Log::info('Reorder completed successfully', [
                'order_id' => $order->order_id,
                'user_id' => Auth::id(),
                'added_items' => $addedItems,
                'unavailable_items' => count($unavailableItems),
                'out_of_stock_items' => count($outOfStockItems)
            ]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'added_items' => $addedItems,
                'cart_url' => route('cart.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error during reorder', [
                'order_id' => $order->order_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi mua lại. Vui lòng thử lại.'
            ], 500);
        }
    }

    /**
     * Helper method to add item to cart
     */
    private function addToCart(User $user, Product $product, $variantId, $quantity)
    {
        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $existingCartItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $product->product_id)
            ->where('variant_id', $variantId)
            ->first();

        // Tính giá
        $price = $product->price;
        if ($variantId && $variantId > 1) {
            $variant = ProductVariant::find($variantId);
            if ($variant) {
                $price += $variant->additional_price;
            }
        }

        if ($existingCartItem) {
            // Cập nhật số lượng
            $existingCartItem->update([
                'quantity' => $existingCartItem->quantity + $quantity,
                'price' => $price // Cập nhật giá mới nhất
            ]);
        } else {
            // Tạo mới
            CartItem::create([
                'user_id' => $user->id,
                'product_id' => $product->product_id,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $price
            ]);
        }
    }
}
