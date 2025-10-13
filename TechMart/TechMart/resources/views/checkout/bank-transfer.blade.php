@extends('layouts.app')

@section('title', 'Hướng dẫn chuyển khoản')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Hướng dẫn chuyển khoản</h1>
            <p class="text-gray-600">Đơn hàng #{{ $order->order_number }}</p>
        </div>

        <!-- Bank Transfer Information -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Thông tin chuyển khoản</h2>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ngân hàng:</span>
                        <span class="font-medium">Vietcombank</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Số tài khoản:</span>
                        <span class="font-medium font-mono">1234567890</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Chủ tài khoản:</span>
                        <span class="font-medium">TECHMART COMPANY</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Chi nhánh:</span>
                        <span class="font-medium">Hà Nội</span>
                    </div>
                    <div class="flex justify-between border-t pt-3">
                        <span class="text-gray-600">Số tiền:</span>
                        <span class="font-bold text-lg text-red-600">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nội dung chuyển khoản:</span>
                        <span class="font-medium font-mono">{{ $order->order_number }}</span>
                    </div>
                </div>
            </div>

            <!-- QR Code (Optional) -->
            <div class="text-center mb-4">
                <div class="inline-block p-4 bg-white border-2 border-gray-200 rounded-lg">
                    <img src="/placeholder.svg?height=200&width=200" alt="QR Code" class="w-48 h-48">
                    <p class="text-sm text-gray-600 mt-2">Quét mã QR để chuyển khoản nhanh</p>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h3 class="font-medium text-yellow-800 mb-2">Lưu ý quan trọng:</h3>
                <ul class="text-sm text-yellow-700 space-y-1">
                    <li>• Vui lòng chuyển khoản đúng số tiền và ghi đúng nội dung</li>
                    <li>• Đơn hàng sẽ được xử lý sau khi chúng tôi nhận được thanh toán</li>
                    <li>• Thời gian xử lý: 1-2 giờ trong giờ hành chính</li>
                    <li>• Liên hệ hotline nếu cần hỗ trợ: 1900-1234</li>
                </ul>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Tóm tắt đơn hàng</h2>
            
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Tạm tính:</span>
                    <span>{{ number_format($order->subtotal, 0, ',', '.') }}₫</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Phí vận chuyển:</span>
                    <span>{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Thuế VAT:</span>
                    <span>{{ number_format($order->tax_amount, 0, ',', '.') }}₫</span>
                </div>
                <div class="flex justify-between text-lg font-semibold border-t pt-2">
                    <span>Tổng cộng:</span>
                    <span class="text-blue-600">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('home') }}" 
               class="flex-1 inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                Về trang chủ
            </a>
            <a href="{{ route('orders.show', $order->id) }}" 
               class="flex-1 inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                Xem đơn hàng
            </a>
        </div>

        <!-- Contact Support -->
        <div class="text-center mt-8 p-4 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600 mb-2">Cần hỗ trợ?</p>
            <div class="space-y-1 text-sm">
                <p><strong>Hotline:</strong> 1900-1234</p>
                <p><strong>Email:</strong> support@techmart.com</p>
                <p><strong>Giờ làm việc:</strong> 8:00 - 22:00 (Thứ 2 - Chủ nhật)</p>
            </div>
        </div>
    </div>
</div>
@endsection