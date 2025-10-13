@extends('layouts.app')

@section('title', 'Đặt hàng bị hủy')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Đặt hàng bị hủy</h1>
        <p class="text-gray-600 mb-8">Đơn hàng của bạn đã bị hủy. Không có khoản phí nào được tính.</p>
        
        <div class="space-y-4">
            <a href="{{ route('cart.index') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                Quay lại giỏ hàng
            </a>
            <br>
            <a href="{{ route('home') }}" 
               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                Về trang chủ
            </a>
        </div>
        
        <div class="mt-8 text-sm text-gray-500">
            <p>Cần hỗ trợ? Liên hệ với chúng tôi qua hotline: <strong>1900-1234</strong></p>
        </div>
    </div>
</div>
@endsection