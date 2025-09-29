<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user instanceof User) {
            abort(401);
        }

        $cartItems = $user->cartItems()->with(['product', 'productVariant'])->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống');
        }

        // Kiểm tra tồn kho
        foreach ($cartItems as $item) {
            if ($item->productVariant && $item->quantity > $item->productVariant->stock_quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "Sản phẩm {$item->product->name} chỉ còn {$item->productVariant->stock_quantity} sản phẩm trong kho");
            }
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        $shipping = $this->calculateShipping($subtotal);
        $tax = $this->calculateTax($subtotal);
        $total = $subtotal + $shipping + $tax;

        return view('checkout.index', compact('cartItems', 'subtotal', 'shipping', 'tax', 'total'));
    }

    public function store(Request $request): RedirectResponse
    {
        Log::info('Checkout request received', $request->all());

        /** @var User $user */
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_district' => 'required|string|max:100',
            'shipping_ward' => 'required|string|max:100',
            'payment_method' => 'required|in:cod,bank_transfer,momo,vnpay',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            Log::warning('Checkout validation failed', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $cartItems = $user->cartItems()->with(['product', 'productVariant'])->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống.');
        }

        DB::beginTransaction();

        try {
            // Kiểm tra tồn kho
            foreach ($cartItems as $item) {
                if ($item->productVariant && $item->quantity > $item->productVariant->stock_quantity) {
                    throw new \Exception("Sản phẩm {$item->product->name} không đủ số lượng trong kho");
                }
            }

            // Tính toán chi phí
            $subtotal = $cartItems->sum(fn($item) => $item->price * $item->quantity);
            $shipping = $this->calculateShipping($subtotal);
            $tax = $this->calculateTax($subtotal);
            $total = $subtotal + $shipping + $tax;

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $this->generateOrderNumber(),
                'order_date' => now(),
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_district' => $request->shipping_district,
                'shipping_ward' => $request->shipping_ward,
                'subtotal' => $subtotal,
                'shipping_fee' => $shipping,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'notes' => $request->notes,
            ]);

            Log::info('Order created successfully', ['order_id' => $order->order_id]);

            // Tạo order items với thông tin variant CHI TIẾT
            foreach ($cartItems as $item) {
                $variantId = $item->variant_id ?: 1;
                $variant = ProductVariant::find($variantId);

                // Kiểm tra xem sản phẩm có biến thể thực sự hay không
                $hasRealVariants = $this->productHasRealVariants($item->product->product_id);

                // Xác định tên biến thể
                $variantName = 'Mặc định';

                // Nếu sản phẩm có biến thể thực sự và biến thể hiện tại có tên
                if ($hasRealVariants && $variant && !empty($variant->variant_name)) {
                    $variantName = $variant->variant_name;
                }

                Log::info('Creating order item', [
                    'product_name' => $item->product->name,
                    'variant_id' => $variantId,
                    'variant_name' => $variantName,
                    'has_real_variants' => $hasRealVariants,
                    'variant_data' => $variant ? $variant->toArray() : null
                ]);

                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item->product->product_id,
                    'variant_id' => $variantId,
                    'product_name' => $item->product->name,
                    'variant_name' => $variantName,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->price * $item->quantity,
                ]);

                // Cập nhật tồn kho
                if ($item->productVariant && $item->variant_id > 0) {
                    $item->productVariant->decrement('stock_quantity', $item->quantity);
                }
            }

            // Xóa giỏ hàng
            $user->cartItems()->delete();

            DB::commit();

            Log::info('Checkout completed successfully', ['order_id' => $order->order_id]);

            return $this->handlePayment($order, $request->payment_method);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());

            return redirect()->route('checkout.index')
                ->with('error', 'Đã xảy ra lỗi khi đặt hàng: ' . $e->getMessage());
        }
    }

    /**
     * Kiểm tra xem sản phẩm có biến thể thực sự hay không
     * 
     * @param int $productId
     * @return bool
     */
    private function productHasRealVariants(int $productId): bool
    {
        // Đếm số lượng biến thể của sản phẩm
        $variantCount = ProductVariant::where('product_id', $productId)->count();

        // Nếu có nhiều hơn 1 biến thể, thì chắc chắn có biến thể thực sự
        if ($variantCount > 1) {
            return true;
        }

        // Nếu chỉ có 1 biến thể, kiểm tra xem có phải biến thể mặc định không
        if ($variantCount == 1) {
            $variant = ProductVariant::where('product_id', $productId)->first();

            // Nếu biến thể có tên khác "Mặc định" hoặc "Phiên bản mặc định"
            // và không phải variant_id = 1, thì coi như có biến thể thực sự
            if (
                $variant &&
                $variant->variant_id != 1 &&
                $variant->variant_name != 'Mặc định' &&
                $variant->variant_name != 'Phiên bản mặc định'
            ) {
                return true;
            }
        }

        // Mặc định coi như không có biến thể thực sự
        return false;
    }

    private function handlePayment(Order $order, string $paymentMethod): RedirectResponse
    {
        switch ($paymentMethod) {
            case 'cod':
                return redirect()->route('checkout.success', ['orderId' => $order->order_id])
                    ->with('success', 'Đặt hàng thành công! Bạn sẽ thanh toán khi nhận hàng.');
            case 'bank_transfer':
                return redirect()->route('checkout.bank-transfer', ['orderId' => $order->order_id]);
            case 'momo':
                return redirect()->route('checkout.momo', ['orderId' => $order->order_id]);
            case 'vnpay':
                return redirect()->route('checkout.vnpay', ['orderId' => $order->order_id]);
            default:
                return redirect()->route('checkout.success', ['orderId' => $order->order_id]);
        }
    }

    private function calculateShipping(float $subtotal): float
    {
        return $subtotal >= 500000 ? 0 : 30000;
    }

    private function calculateTax(float $subtotal): float
    {
        return $subtotal * 0.1;
    }

    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'TM' . date('Ymd') . rand(1000, 9999);
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    public function success($orderId): View
    {
        $order = Order::with(['orderItems.product', 'orderItems.productVariant'])
            ->where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('checkout.success', compact('order'));
    }

    public function cancel(): View
    {
        return view('checkout.cancel');
    }

    public function bankTransfer($orderId): View
    {
        $order = Order::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('checkout.bank-transfer', compact('order'));
    }

    public function momo($orderId): View
    {
        $order = Order::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('checkout.momo', compact('order'));
    }

    public function vnpay($orderId): View
    {
        $order = Order::where('order_id', $orderId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('checkout.vnpay', compact('order'));
    }
}
