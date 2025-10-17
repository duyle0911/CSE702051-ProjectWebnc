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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http; // Đảm bảo
class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Thêm mới Momo
    public function momoPayment($orderId)
{
    $endpoint = env('MOMO_ENDPOINT') . "/v2/gateway/api/create";
    $partnerCode = env('MOMO_PARTNER_CODE');
    $accessKey = env('MOMO_ACCESS_KEY');
    $secretKey = env('MOMO_SECRET_KEY');

    // Tìm đơn hàng theo order_id
    $order = Order::findOrFail($orderId);

    $amount = $order->total_amount;
    $orderInfo = "Thanh toán đơn hàng TechMart - Mã: {$order->order_number}";
    $orderIdMomo = $order->order_number;

$redirectUrl = route('payment.momo.callback'); // GET, để xử lý kết quả user thấy
$ipnUrl = route('payment.momo.notify');       // POST, để MoMo gửi kết quả ngầm

    $extraData = "";

    $requestId = $orderIdMomo;
    $requestType = "captureWallet";

    $rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderIdMomo&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";
    $signature = hash_hmac("sha256", $rawHash, $secretKey);

    $data = [
        'partnerCode' => $partnerCode,
        'accessKey' => $accessKey,
        'requestId' => $requestId,
        'amount' => $amount,
        'orderId' => $orderIdMomo,
        'orderInfo' => $orderInfo,
        'redirectUrl' => $redirectUrl,
        'ipnUrl' => $ipnUrl,
        'extraData' => $extraData,
        'requestType' => $requestType,
        'signature' => $signature,
        'lang' => 'vi',
    ];

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $result = curl_exec($ch);
    curl_close($ch);

    $jsonResult = json_decode($result, true);
if (!empty($jsonResult['payUrl'])) {
    return redirect($jsonResult['payUrl']);
} else {
    return redirect()->back()->with('error', 'Không thể kết nối tới MoMo');
}}
//

//
public function momoReturn(Request $request)
{
    if ($request->get('resultCode') == 0) {
        $orderId = $request->get('orderId');
        $order = Order::where('order_number', $orderId)->first();

        if ($order) {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
            ]);
        }

        return view('checkout.success', ['message' => 'Thanh toán Momo thành công!']);
    } else {
        return view('checkout.fail', ['message' => 'Thanh toán thất bại hoặc bị hủy.']);
    }
}
//
public function redirectToMomo(Order $order)
{
    $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";

    $partnerCode = "MOMOXXXXXX"; // từ sandbox
    $accessKey = "your_access_key";
    $secretKey = "your_secret_key";

    $orderId = time() . ""; // ID đơn hàng
    $orderInfo = "Thanh toán đơn hàng #" . $order->id;
    $redirectUrl = route('payment.momo.callback');
    $ipnUrl = route('payment.momo.notify');
    $amount = $order->total;
    $extraData = "";

    $requestId = time() . "";
    $requestType = "captureWallet";

    // Raw hash
    $rawHash = "accessKey=$accessKey&amount=$amount&extraData=$extraData&ipnUrl=$ipnUrl&orderId=$orderId&orderInfo=$orderInfo&partnerCode=$partnerCode&redirectUrl=$redirectUrl&requestId=$requestId&requestType=$requestType";

    $signature = hash_hmac("sha256", $rawHash, $secretKey);

    $data = [
        'partnerCode' => $partnerCode,
        'accessKey' => $accessKey,
        'requestId' => $requestId,
        'amount' => $amount,
        'orderId' => $orderId,
        'orderInfo' => $orderInfo,
        'redirectUrl' => $redirectUrl,
        'ipnUrl' => $ipnUrl,
        'extraData' => $extraData,
        'requestType' => $requestType,
        'signature' => $signature,
        'lang' => 'vi'
    ];

    $response = Http::post($endpoint, $data)->json();

    if (!empty($response['payUrl'])) {
        return redirect($response['payUrl']);
    }

    return back()->with('error', 'Không thể kết nối đến cổng MoMo');
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
            'payment_method' => 'required|in:cod,bank_transfer,momo,vnpay,stripe',
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
                return redirect()->route('checkout.vnpay', ['orderId' => $order->order_id])
                    ->with('success', 'Đặt hàng thành công! Bạn sẽ thanh toán khi nhận hàng.');   
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


public function vnpay($orderId)
{
    $order = Order::findOrFail($orderId);

    $vnp_TmnCode = env('VNP_TMNCODE');
    $vnp_HashSecret = env('VNP_HASHSECRET');
    $vnp_Url = env('VNP_URL');
    $vnp_Returnurl = env('VNP_RETURNURL');

    $vnp_TxnRef = $order->order_id; // dùng mã đơn thật
    $vnp_OrderInfo = "Thanh toán đơn hàng #" . $order->order_id;
    $vnp_OrderType = "billpayment";
    $vnp_Amount = $order->total_amount * 100; // ✅ giá trị động
    $vnp_Locale = "vn";
 //   $vnp_BankCode = "NCB";
    $vnp_IpAddr = request()->ip();

    $inputData = [
        "vnp_Version" => "2.1.0",
        "vnp_TmnCode" => $vnp_TmnCode,
        "vnp_Amount" => $vnp_Amount,
        "vnp_Command" => "pay",
        "vnp_CreateDate" => date('YmdHis'),
        "vnp_CurrCode" => "VND",
        "vnp_IpAddr" => $vnp_IpAddr,
        "vnp_Locale" => $vnp_Locale,
        "vnp_OrderInfo" => $vnp_OrderInfo,
        "vnp_OrderType" => $vnp_OrderType,
        "vnp_ReturnUrl" => $vnp_Returnurl,
        "vnp_TxnRef" => $vnp_TxnRef,
     //   "vnp_BankCode" => $vnp_BankCode
    ];

    ksort($inputData);
    $hashdata = '';
    $query = '';
    $i = 0;
    foreach ($inputData as $key => $value) {
        if ($i == 1) {
            $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashdata .= urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }
        $query .= urlencode($key) . "=" . urlencode($value) . '&';
    }

    $hashdata = rtrim($hashdata, '&');
    $query = rtrim($query, '&');

    $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
    $vnp_Url .= "?" . $query . '&vnp_SecureHash=' . $vnp_SecureHash;

    return redirect($vnp_Url);
}
/*
        public function returnPayment(Request $request)
{
    $vnp_ResponseCode = $request->input('vnp_ResponseCode'); // '00' là thành công
    $vnp_TxnRef = $request->input('vnp_TxnRef'); // mã đơn hàng

    if ($vnp_ResponseCode == '00') {
        // Tìm đơn hàng theo mã
        $order = \App\Models\Order::where('order_id', $vnp_TxnRef)->first();

        // Cập nhật trạng thái nếu cần
        if ($order && $order->status !== 'paid') {
            $order->status = 'paid';
            $order->payment_method = 'vnpay';
            $order->save();
        }

        return view('checkout.success', compact('order'));
    }

    return view('checkout.fail');
}

*/

public function returnPayment(Request $request)
{
    // Lấy dữ liệu từ VNPay
    $vnp_ResponseCode = $request->input('vnp_ResponseCode'); // '00' là thành công
    $vnp_TxnRef = $request->input('vnp_TxnRef');             // Mã đơn hàng (order_id)

    // Kiểm tra mã phản hồi
    if ($vnp_ResponseCode === '00') {
        // Tìm đơn hàng
        $order = Order::where('order_id', $vnp_TxnRef)->first();

        if ($order && $order->payment_status !== 'paid') {
            // Cập nhật trạng thái đơn hàng
            $order->payment_status = 'paid';        // Đã thanh toán
            $order->payment_method = 'vnpay';       // Cổng thanh toán
            $order->status = 'confirmed';           // Chuyển sang trạng thái xác nhận
            $order->save();
        }

        return view('checkout.success', compact('order'));
    }

    // Nếu thanh toán thất bại
    return view('checkout.fail');
}



}