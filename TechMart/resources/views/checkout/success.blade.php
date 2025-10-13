@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Success Message -->
                <div class="text-center mb-5">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 64px; height: 64px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" stroke="green"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check">
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h1 class="h3 fw-bold text-dark">Đặt hàng thành công!</h1>
                    <p class="text-muted">Cảm ơn bạn đã mua hàng tại TechMart</p>
                </div>

                <!-- Order Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Thông tin đơn hàng</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-semibold">Chi tiết đơn hàng</h6>
                                <ul class="list-unstyled small">
                                    <li><strong>Mã đơn hàng:</strong>
                                        @if (isset($order->order_number))
                                            {{ $order->order_number }}
                                        @else
                                            #{{ $order->id }}
                                        @endif
                                    </li>
                                    <li><strong>Ngày đặt:</strong> {{ $order->order_date->format('d/m/Y H:i') }}</li>
                                    <li><strong>Trạng thái:</strong> <span
                                            class="badge bg-warning text-dark">{{ $order->status_label }}</span></li>
                                    @if (isset($order->payment_method))
                                        <li><strong>Phương thức thanh toán:</strong>
                                            @switch($order->payment_method)
                                                @case('cod')
                                                    Thanh toán khi nhận hàng
                                                @break

                                                @case('bank_transfer')
                                                    Chuyển khoản ngân hàng
                                                @break

                                                @case('momo')
                                                    Ví MoMo
                                                @break

                                                @case('vnpay')
                                                    VNPay
                                                @break

                                                @default
                                                    {{ $order->payment_method }}
                                            @endswitch
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-semibold">Thông tin giao hàng</h6>
                                <ul class="list-unstyled small">
                                    @if (isset($order->shipping_name))
                                        <li class="fw-bold">{{ $order->shipping_name }}</li>
                                        <li>{{ $order->shipping_phone }}</li>
                                        <li>{{ $order->shipping_address ?? 'Địa chỉ chưa cập nhật' }}</li>
                                        @if (isset($order->shipping_ward))
                                            <li>{{ $order->shipping_ward }}, {{ $order->shipping_district }},
                                                {{ $order->shipping_city }}</li>
                                        @endif
                                    @else
                                        <li>{{ $order->shipping_address ?? 'Thông tin giao hàng chưa đầy đủ' }}</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Sản phẩm đã đặt</h5>
                        @foreach ($order->orderItems as $item)
                            <div class="d-flex align-items-center border-bottom py-3">
                                <div class="flex-shrink-0 me-3">
                                    @if ($item->product)
                                        {{-- Sửa ảnh đoạn này --}}
                                        <img src="{{ $item->product->image_url
                                            ? asset('storage/' . $item->product->image_url)
                                            : url('/placeholder.svg?height=80&width=80') }}"
                                            alt="{{ $item->product_name ?? $item->product->name }}" class="img-thumbnail"
                                            style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="bg-light text-muted d-flex align-items-center justify-content-center rounded"
                                            style="width: 80px; height: 80px;">No Image</div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">
                                        {{ $item->product_name ?? ($item->product ? $item->product->name : 'Sản phẩm không xác định') }}
                                    </div>
                                    @if (isset($item->variant_name) && $item->variant_name)
                                        <div class="small text-muted">{{ $item->variant_name }}</div>
                                    @endif
                                    <div class="small">{{ number_format($item->price, 0, ',', '.') }}₫ ×
                                        {{ $item->quantity }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-semibold">
                                        {{ number_format($item->total ?? $item->price * $item->quantity, 0, ',', '.') }}₫
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Totals -->
                        <div class="pt-4 mt-3 border-top">
                            <ul class="list-unstyled small">
                                @if (isset($order->subtotal))
                                    <li class="d-flex justify-content-between">
                                        <span>Tạm tính:</span>
                                        <span>{{ number_format($order->subtotal, 0, ',', '.') }}₫</span>
                                    </li>
                                @endif
                                @if (isset($order->shipping_fee))
                                    <li class="d-flex justify-content-between">
                                        <span>Phí vận chuyển:</span>
                                        <span>{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</span>
                                    </li>
                                @endif
                                @if (isset($order->tax_amount))
                                    <li class="d-flex justify-content-between">
                                        <span>Thuế VAT:</span>
                                        <span>{{ number_format($order->tax_amount, 0, ',', '.') }}₫</span>
                                    </li>
                                @endif
                                <li class="d-flex justify-content-between fw-bold fs-5 mt-2 border-top pt-2">
                                    <span>Tổng cộng:</span>
                                    <span
                                        class="text-primary">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center mb-4">
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">Tiếp tục mua hàng</a>
                </div>

                <!-- Contact Information -->
                <div class="text-center small text-muted">
                    <p>Có thắc mắc về đơn hàng? Liên hệ với chúng tôi:</p>
                    <p><strong>Hotline:</strong> 1900-1234 | <strong>Email:</strong> support@techmart.com</p>
                </div>
            </div>
        </div>
    </div>
@endsection
