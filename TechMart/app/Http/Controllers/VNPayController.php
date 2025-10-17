<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class VNPayController extends Controller
{
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
  //  $vnp_BankCode = "NCB";
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
      //  "vnp_BankCode" => $vnp_BankCode
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
    // Lấy dữ liệu từ VNPay
    $vnp_ResponseCode = $request->input('vnp_ResponseCode'); // '00' là thành công
    $vnp_TxnRef = $request->input('vnp_TxnRef');             // Mã đơn hàng (order_id)

    // Kiểm tra mã phản hồi
    if ($vnp_ResponseCode === '00') {
        // Tìm đơn hàng
      //  $order = Order::where('order_id', $vnp_TxnRef)->first();
        $order = \App\Models\Order::where('order_id', $vnp_TxnRef)->first();

        if ($order && $order->payment_status !== 'paid') {
            // Cập nhật trạng thái đơn hàng
            $order->payment_status = 'paid';        // Đã thanh toán
            $order->payment_method = 'vnpay';       // Cổng thanh toán
            $order->status = 'confirmed';           // Chuyển sang trạng thái xác nhận
            $order->save();
        }

   //     return view('checkout.success', compact('order'));
           return redirect('/checkout/success/' . $order->order_id);

    }

    // Nếu thanh toán thất bại
    return view('checkout.fail');
}
*/
public function returnPayment(Request $request)
{
    $vnp_ResponseCode = $request->input('vnp_ResponseCode'); // '00' là thành công
    $vnp_TxnRef = $request->input('vnp_TxnRef');             // Mã đơn hàng (order_id)

    // Nếu thanh toán thành công
    if ($vnp_ResponseCode === '00') {
        $order = Order::where('order_id', $vnp_TxnRef)->first();

        if ($order) {
            // Nếu chưa cập nhật trạng thái thanh toán
            if ($order->payment_status !== 'paid') {
                $order->payment_status = 'paid';
                $order->payment_method = 'vnpay';
                $order->status = 'confirmed';
                $order->save();
            }

            // ✅ Chuyển hướng về trang thành công, truyền id qua route
            return redirect()->route('checkout.success', ['orderId' => $order->order_id]);
        }

        // ❌ Nếu không tìm thấy đơn hàng
        return view('checkout.fail')->with('message', 'Không tìm thấy đơn hàng');
    }

    // ❌ Nếu mã phản hồi không thành công
    return view('checkout.fail')->with('message', 'Thanh toán thất bại');
}

}
