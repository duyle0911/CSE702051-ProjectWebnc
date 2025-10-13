@extends('layouts.app')

@section('title', 'Thanh toán - TechMart')

@push('styles')
    <style>
        .checkout-container {
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 100%);
            min-height: 100vh;
            padding: 1.5rem 0;
        }

        .checkout-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #f3f4f6;
            transition: all 0.3s ease;
        }

        .checkout-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .section-header {
            background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
            color: white;
            padding: 1.25rem 1.5rem;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
            border-radius: 12px 12px 0 0;
        }

        .section-header h4 {
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
            letter-spacing: 0.025em;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
            letter-spacing: 0.01em;
        }

        .form-control,
        .form-select {
            font-size: 0.875rem;
            padding: 0.75rem 1rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.2s ease;
            background: #fafafa;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
            background: white;
        }

        .form-control.is-invalid,
        .form-select.is-invalid {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        .payment-option {
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fafafa;
            position: relative;
        }

        .payment-option:hover {
            border-color: #dc2626;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.1);
        }

        .payment-option.active {
            border-color: #dc2626;
            background: linear-gradient(135deg, #ffffff, #fdefef);
            color: rgb(0, 0, 0);
        }

        /* Disabled payment options */
        .payment-option.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f9f9f9;
            border-color: #d1d5db;
            position: relative;
        }

        .payment-option.disabled:hover {
            transform: none;
            box-shadow: none;
            border-color: #d1d5db;
        }

        .payment-option.disabled input[type="radio"] {
            pointer-events: none;
        }

        /* Tooltip for disabled options */
        .payment-option.disabled::after {
            content: "Phương thức thanh toán đang được bảo trì";
            position: absolute;
            top: -40px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            z-index: 1000;
        }

        .payment-option.disabled::before {
            content: "";
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid rgba(0, 0, 0, 0.8);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            z-index: 1000;
        }

        .payment-option.disabled:hover::after,
        .payment-option.disabled:hover::before {
            opacity: 1;
        }

        .payment-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .payment-option .fw-bold {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .payment-option small {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .payment-icon {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .order-summary {
            background: linear-gradient(135deg, #ffffff, #ffffff);
            border-radius: 12px;
            padding: 1.5rem;
            position: sticky;
            top: 2rem;
            border: 1px solid #fecaca;
        }

        .order-summary h4 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1.5rem;
        }

        .cart-item {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border: 1px solid #f3f4f6;
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transform: translateY(-1px);
        }

        .cart-item h6 {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1f2937;
            line-height: 1.3;
            margin-bottom: 0.25rem;
        }

        .cart-item small {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .btn-checkout {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            border: none;
            border-radius: 10px;
            padding: 0.875rem 1.5rem;
            font-weight: 600;
            color: white;
            width: 100%;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-transform: none;
            letter-spacing: 0.025em;
        }

        .btn-checkout:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.3);
            color: white;
        }

        .btn-checkout:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .loading-spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .progress-steps {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            flex: 1;
            max-width: 140px;
        }

        .step-circle {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
            font-size: 0.95rem;
        }

        .step.active .step-circle {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: white;
        }

        .step.completed .step-circle {
            background: #f47171;
            color: white;
        }

        .step span {
            font-size: 0.8rem;
            font-weight: 500;
            color: #6b7280;
        }

        .step.active span {
            color: #dc2626;
            font-weight: 600;
        }

        .step-line {
            position: absolute;
            top: 22px;
            left: 50%;
            right: -50%;
            height: 2px;
            background: #e5e7eb;
            z-index: 1;
        }

        .step:last-child .step-line {
            display: none;
        }

        .step.completed .step-line {
            background: linear-gradient(135deg, #dc6666, #ea4d4d);
        }

        .alert-custom {
            border-radius: 10px;
            border-left: 4px solid;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .shipping-notice {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border: 1px solid #fca5a5;
            border-radius: 10px;
            padding: 0.875rem;
            text-align: center;
            margin-top: 1rem;
        }

        .shipping-notice.free {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-color: #6ee7b7;
        }

        .shipping-notice small {
            font-size: 0.8rem;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border-left-color: #ef4444;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border-left-color: #22c55e;
        }

        .text-primary {
            color: #dc2626 !important;
        }

        .bg-primary {
            background-color: #dc2626 !important;
        }

        .border-primary {
            border-color: #dc2626 !important;
        }

        /* Typography improvements */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-weight: 600;
            line-height: 1.3;
        }

        .fw-bold {
            font-weight: 600 !important;
        }

        .fw-semibold {
            font-weight: 500 !important;
        }

        /* Spacing adjustments */
        .mb-4 {
            margin-bottom: 1.25rem !important;
        }

        .p-4 {
            padding: 1.25rem !important;
        }

        /* Order totals styling */
        .order-summary .d-flex {
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .order-summary .fs-5 {
            font-size: 1rem !important;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }

        @media (max-width: 768px) {
            .checkout-container {
                padding: 1rem 0;
            }

            .order-summary {
                position: static;
                margin-top: 1.5rem;
            }

            .progress-steps {
                margin-bottom: 1.5rem;
            }

            .step {
                max-width: 90px;
            }

            .step-circle {
                width: 35px;
                height: 35px;
                font-size: 0.8rem;
            }

            .step-line {
                top: 17px;
            }

            .payment-icon {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }

            .section-header h4 {
                font-size: 0.95rem;
            }

            .order-summary h4 {
                font-size: 1rem;
            }

            .payment-option.disabled::after {
                font-size: 11px;
                padding: 6px 10px;
                top: -35px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="checkout-container">
        <div class="container">
            <!-- Progress Steps -->
            <div class="progress-steps">
                <div class="step completed">
                    <div class="step-circle">
                        <i class="fas fa-check"></i>
                    </div>
                    <span class="small fw-bold">Giỏ hàng</span>
                    <div class="step-line"></div>
                </div>
                <div class="step active">
                    <div class="step-circle">2</div>
                    <span class="small fw-bold">Thanh toán</span>
                    <div class="step-line"></div>
                </div>
                <div class="step">
                    <div class="step-circle">3</div>
                    <span class="small fw-bold">Hoàn thành</span>
                </div>
            </div>

            <!-- Alert Messages -->
            @if (session('error'))
                <div class="alert alert-danger alert-custom">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-custom">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-custom">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Vui lòng kiểm tra lại thông tin:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                @csrf

                <div class="row g-4">
                    <!-- Left Column - Form -->
                    <div class="col-lg-8">

                        <!-- Shipping Information -->
                        <div class="card checkout-card mb-4">
                            <div class="card-body p-4">
                                <div class="section-header">
                                    <h4 class="mb-0 fw-bold">
                                        <i class="fas fa-shipping-fast me-2"></i>
                                        Thông tin giao hàng
                                    </h4>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="shipping_name" class="form-label fw-semibold">
                                            Họ và tên <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control form-control-lg" id="shipping_name"
                                            name="shipping_name" value="{{ old('shipping_name', auth()->user()->name) }}"
                                            required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="shipping_phone" class="form-label fw-semibold">
                                            Số điện thoại <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" class="form-control form-control-lg" id="shipping_phone"
                                            name="shipping_phone"
                                            value="{{ old('shipping_phone', auth()->user()->phone ?? '') }}" required>
                                    </div>
                                </div>
                                {{--  --}}
                                <!-- Địa chỉ chi tiết -->




                                <div class="mb-3">
                                    <label for="shipping_address" class="form-label fw-semibold">
                                        Địa chỉ chi tiết <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control form-control-lg" id="shipping_address" name="shipping_address" rows="3"
                                        placeholder="Số nhà, tên đường, khu vực..." required>{{ old('shipping_address', auth()->user()->address ?? '') }}</textarea>
                                </div>

                                <div class="row g-3">
                                    {{--  --}}

                                    <div class="col-md-4">
                                        <label for="shipping_city" class="form-label fw-semibold">
                                            Tỉnh/Thành phố <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select form-select-lg" id="shipping_city" name="shipping_city"
                                            required>
                                            <option value="">Chọn tỉnh/thành phố</option>
                                            {{-- Các option sẽ được load bằng JavaScript từ API --}}
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="shipping_district" class="form-label fw-semibold">
                                            Quận/Huyện <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select form-select-lg" id="shipping_district"
                                            name="shipping_district" required>
                                            <option value="">Chọn quận/huyện</option>
                                            {{-- Sẽ được load sau khi chọn tỉnh --}}
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="shipping_ward" class="form-label fw-semibold">
                                            Phường/Xã <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select form-select-lg" id="shipping_ward" name="shipping_ward"
                                            required>
                                            <option value="">Chọn phường/xã</option>
                                            {{-- Sẽ được load sau khi chọn quận --}}
                                        </select>
                                    </div>







                                    {{--  --}}
                                </div>
                            </div>
                        </div>






                        <!-- Payment Method -->
                        <div class="card checkout-card mb-4">
                            <div class="card-body p-4">
                                <div class="section-header">
                                    <h4 class="mb-0 fw-bold">
                                        <i class="fas fa-credit-card me-2"></i>
                                        Phương thức thanh toán
                                    </h4>
                                </div>

                                <div class="payment-methods">
                                    <div class="row g-3">
                                        <!-- COD - Available -->
                                        <div class="col-md-6">
                                            <label class="payment-option h-100" data-method="cod">
                                                <input type="radio" name="payment_method" value="cod"
                                                    {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }}>
                                                <div class="d-flex align-items-center h-100">
                                                    <div class="payment-icon bg-danger bg-opacity-10 text-danger">
                                                        <i class="fas fa-money-bill-wave"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">Thanh toán khi nhận hàng (COD)</div>
                                                        <small class="text-muted">Thanh toán bằng tiền mặt khi nhận
                                                            hàng</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>

                                        <!-- Bank Transfer - Disabled -->
                                        <div class="col-md-6">
                                            <div class="payment-option disabled h-100" data-method="bank_transfer">
                                                <input type="radio" name="payment_method" value="bank_transfer"
                                                    disabled>
                                                <div class="d-flex align-items-center h-100">
                                                    <div class="payment-icon"
                                                        style="background-color: rgba(209, 213, 219, 0.3); color: #9ca3af;">
                                                        <i class="fas fa-university"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-muted">Chuyển khoản ngân hàng</div>
                                                        <small class="text-muted">Chuyển khoản qua tài khoản ngân
                                                            hàng</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- MoMo - Disabled -->
                                        {{-- Sửa disabled  --}}
                                        <div class="col-md-6">
                                            <div class="payment-option disabled h-100" data-method="momo">
                                                <input type="radio" name="payment_method" value="momo" disabled>
                                                <div class="d-flex align-items-center h-100">
                                                    <div class="payment-icon"
                                                        style="background-color: rgba(209, 213, 219, 0.3); color: #9ca3af;">
                                                        <i class="fab fa-cc-mastercard"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-muted">Ví MoMo</div>
                                                        <small class="text-muted">Thanh toán qua ví điện tử MoMo</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- VNPay - Disabled -->
                                        <div class="col-md-6">
                                            <div class="payment-option  h-100" data-method="vnpay">
                                                <input type="radio" name="payment_method" value="vnpay">

                                                <div class="d-flex align-items-center h-100">
                                                    <div class="payment-icon bg-danger bg-opacity-10 text-danger">
                                                        <i class="fas fa-credit-card"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-muted">VNPay</div>
                                                        <small class="text-muted">Thanh toán qua cổng VNPay</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>




                                        {{-- Thêm stripe --}}
                                        <div class="col-md-6">
                                            <label class="payment-option  disabled h-100" data-method="stripe">
                                                <input type="radio" name="payment_method" value="stripe" disabled>
                                                <div class="d-flex align-items-center h-100">
                                                    <div class="payment-icon"
                                                        style="background-color: rgba(209, 213, 219, 0.3); color: #9ca3af;">
                                                        <i class="fab fa-cc-stripe"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">Stripe</div>
                                                        <small class="text-muted">Thanh toán bằng thẻ quốc tế
                                                            (Visa/Master)</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>

                                        {{-- end --}}
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Order Notes -->
                        <div class="card checkout-card">
                            <div class="card-body p-4">
                                <div class="section-header">
                                    <h4 class="mb-0 fw-bold">
                                        <i class="fas fa-sticky-note me-2"></i>
                                        Ghi chú đơn hàng
                                    </h4>
                                </div>
                                <textarea class="form-control form-control-lg" name="notes" rows="4"
                                    placeholder="Ghi chú thêm cho đơn hàng (tùy chọn)...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Order Summary -->
                    <div class="col-lg-4">
                        <div class="order-summary">
                            <h4 class="text-center mb-4 fw-bold">
                                <i class="fas fa-receipt me-2"></i>
                                Đơn hàng của bạn
                            </h4>

                            <!-- Cart Items -->
                            <div class="cart-items mb-4">
                                @foreach ($cartItems as $item)
                                    <div class="cart-item">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                {{-- Sửa ảnh đoạn này --}}
                                                <img src="{{ asset('storage/' . ($item->product->image_url ?? 'placeholder.svg')) }}"
                                                    alt="{{ $item->product->name }}" class="rounded border"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1 fw-semibold">{{ $item->product->name }}</h6>
                                                @if ($item->productVariant && $item->productVariant->variant_name != 'Mặc định')
                                                    <small
                                                        class="text-muted">{{ $item->productVariant->variant_name }}</small>
                                                @endif
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <span class="badge bg-secondary">{{ $item->quantity }}x</span>
                                                    <strong
                                                        class="text-primary">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Order Totals -->
                            <div class="border-top pt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Tạm tính:</span>
                                    <span class="fw-semibold">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Phí vận chuyển:</span>
                                    <span class="fw-semibold">
                                        @if ($shipping > 0)
                                            {{ number_format($shipping, 0, ',', '.') }}₫
                                        @else
                                            <span class="text-success fw-bold">Miễn phí</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Thuế VAT:</span>
                                    <span class="fw-semibold">{{ number_format($tax, 0, ',', '.') }}₫</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fs-5 fw-bold">
                                    <span>Tổng cộng:</span>
                                    <span class="text-primary">{{ number_format($total, 0, ',', '.') }}₫</span>
                                </div>
                            </div>

                            <!-- Free Shipping Notice -->
                            @if ($subtotal < 500000)
                                <div class="shipping-notice">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    <small class="fw-semibold">
                                        Mua thêm {{ number_format(500000 - $subtotal, 0, ',', '.') }}₫ để được miễn phí vận
                                        chuyển!
                                    </small>
                                </div>
                            @else
                                <div class="shipping-notice free">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <small class="fw-bold text-success">
                                        Bạn được miễn phí vận chuyển!
                                    </small>
                                </div>
                            @endif

                            <!-- Place Order Button -->
                            <button type="submit" id="place-order-btn" class="btn btn-checkout mt-4">
                                <div class="loading-spinner" id="btn-loading"></div>
                                <span id="btn-text">
                                    <i class="fas fa-lock me-2"></i>
                                    Đặt hàng ngay
                                </span>
                            </button>

                            <!-- Security Notice -->
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Thông tin của bạn được bảo mật và mã hóa
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection



@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form elements
            const form = document.getElementById('checkout-form');
            const submitBtn = document.getElementById('place-order-btn');
            const btnText = document.getElementById('btn-text');
            const btnLoading = document.getElementById('btn-loading');
            const paymentOptions = document.querySelectorAll('.payment-option:not(.disabled)');

            // Payment method selection (only for enabled options)
            paymentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove active class from all options
                    document.querySelectorAll('.payment-option').forEach(opt => opt.classList
                        .remove('active'));

                    // Add active class to clicked option
                    this.classList.add('active');

                    // Check the radio button
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio && !radio.disabled) {
                        radio.checked = true;
                    }
                });
            });

            // Prevent clicking on disabled payment options
            document.querySelectorAll('.payment-option.disabled').forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });

            // Set initial active payment method (COD)
            const codOption = document.querySelector('.payment-option[data-method="cod"]');
            if (codOption) {
                codOption.classList.add('active');
                const codRadio = codOption.querySelector('input[type="radio"]');
                if (codRadio) {
                    codRadio.checked = true;
                }
            }

            // Form submission
            form.addEventListener('submit', function(e) {
                // Show loading state
                submitBtn.disabled = true;
                btnText.style.display = 'none';
                btnLoading.style.display = 'inline-block';

                // Basic validation
                const requiredFields = form.querySelectorAll(
                    'input[required], select[required], textarea[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        field.focus();
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                // Phone validation
                const phoneField = document.getElementById('shipping_phone');
                const phoneRegex = /^[0-9]{10,11}$/;
                if (!phoneRegex.test(phoneField.value.replace(/\s/g, ''))) {
                    isValid = false;
                    phoneField.classList.add('is-invalid');
                    showToast('Số điện thoại không hợp lệ', 'error');
                }

                // Payment method validation
                const checkedPayment = document.querySelector('input[name="payment_method"]:checked');
                if (!checkedPayment || checkedPayment.disabled) {
                    isValid = false;
                    showToast('Vui lòng chọn phương thức thanh toán', 'error');
                }

                if (!isValid) {
                    e.preventDefault();
                    // Reset button state
                    submitBtn.disabled = false;
                    btnText.style.display = 'inline';
                    btnLoading.style.display = 'none';
                    return;
                }

                // Auto re-enable button after 15 seconds
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.disabled = false;
                        btnText.style.display = 'inline';
                        btnLoading.style.display = 'none';
                    }
                }, 15000);
            });

            // Real-time validation
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });

                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            // Phone number formatting
            const phoneInput = document.getElementById('shipping_phone');
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);

                // Format phone number
                if (value.length >= 7) {
                    value = value.replace(/(\d{3})(\d{3})(\d{4})/, '$1 $2 $3');
                } else if (value.length >= 4) {
                    value = value.replace(/(\d{3})(\d{3})/, '$1 $2');
                }

                e.target.value = value;
            });



            // Toast notification function
            function showToast(message, type = 'info') {
                const toastContainer = document.getElementById('toast-container') || createToastContainer();

                const toast = document.createElement('div');
                toast.className =
                    `toast align-items-center text-white bg-${type === 'error' ? 'danger' : 'primary'} border-0`;
                toast.setAttribute('role', 'alert');
                toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

                toastContainer.appendChild(toast);

                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();

                toast.addEventListener('hidden.bs.toast', () => {
                    toast.remove();
                });
            }

            function createToastContainer() {
                const container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'toast-container position-fixed top-0 end-0 p-3';
                container.style.zIndex = '9999';
                document.body.appendChild(container);
                return container;
            }
        });
    </script>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const citySelect = document.getElementById('shipping_city');
            const districtSelect = document.getElementById('shipping_district');
            const wardSelect = document.getElementById('shipping_ward');

            // Load danh sách tỉnh/thành phố
            fetch('https://provinces.open-api.vn/api/p/')
                .then(res => res.json())
                .then(data => {
                    data.forEach(city => {
                        const selected = city.name === "{{ old('shipping_city') }}" ? 'selected' : '';
                        citySelect.innerHTML +=
                            `<option value="${city.name}" ${selected}>${city.name}</option>`;
                    });

                    if ("{{ old('shipping_city') }}") {
                        citySelect.dispatchEvent(new Event('change'));
                    }
                });

            // Load quận/huyện khi chọn tỉnh
            citySelect.addEventListener('change', function() {
                const cityName = this.value;
                districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
                wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
                if (!cityName) return;

                fetch('https://provinces.open-api.vn/api/p/')
                    .then(res => res.json())
                    .then(provinces => {
                        const selected = provinces.find(p => p.name === cityName);
                        return fetch(`https://provinces.open-api.vn/api/p/${selected.code}?depth=2`);
                    })
                    .then(res => res.json())
                    .then(data => {
                        data.districts.forEach(d => {
                            const selected = d.name === "{{ old('shipping_district') }}" ?
                                'selected' : '';
                            districtSelect.innerHTML +=
                                `<option value="${d.name}" ${selected}>${d.name}</option>`;
                        });

                        if ("{{ old('shipping_district') }}") {
                            districtSelect.dispatchEvent(new Event('change'));
                        }
                    });
            });

            // Load phường/xã khi chọn quận
            districtSelect.addEventListener('change', function() {
                const cityName = citySelect.value;
                const districtName = this.value;
                wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
                if (!cityName || !districtName) return;

                fetch('https://provinces.open-api.vn/api/p/')
                    .then(res => res.json())
                    .then(provinces => {
                        const selected = provinces.find(p => p.name === cityName);
                        return fetch(`https://provinces.open-api.vn/api/p/${selected.code}?depth=3`);
                    })
                    .then(res => res.json())
                    .then(data => {
                        const selectedDistrict = data.districts.find(d => d.name === districtName);
                        if (!selectedDistrict) return;

                        selectedDistrict.wards.forEach(w => {
                            const selected = w.name === "{{ old('shipping_ward') }}" ?
                                'selected' : '';
                            wardSelect.innerHTML +=
                                `<option value="${w.name}" ${selected}>${w.name}</option>`;
                        });
                    });
            });
        });
    </script>
@endpush
