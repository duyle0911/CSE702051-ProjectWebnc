<?php

// Thêm mới 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeCheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        // Khóa bảo mật từ .env
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Tạo session thanh toán
        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'vnd',
                    'product_data' => [
                        'name' => 'Sản phẩm demo',
                    ],
                    'unit_amount' => 100000 * 100, // 100000 VND × 100 = 10000000 đ
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel'),
        ]);

        // Chuyển hướng tới Stripe Checkout
        return redirect($session->url);
    }

    public function success()
    {
        return view('checkout.success');
    }

    public function cancel()
    {
        return view('checkout.cancel');
    }
}