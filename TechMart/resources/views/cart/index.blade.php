@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">🛒 Giỏ hàng của bạn</h1>

    @if($cartItems->count() > 0)
        <div class="row gy-4">
            @foreach($cartItems as $item)
                <div class="card p-3 d-flex flex-row align-items-center">
                    <!-- Product Image -->
                    <div class="me-3">
                        @if($item->product->image_url)
                            <img src="{{ asset('storage/' . $item->product->image_url) }}" 
                                 alt="{{ $item->product->name }}" 
                                 class="img-thumbnail" 
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="bg-light d-flex justify-content-center align-items-center" style="width: 100px; height: 100px;">
                                <i class="fas fa-image text-muted fs-3"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="flex-grow-1">
                        <h5 class="mb-1">{{ $item->product->name }}</h5>
                        @if($item->productVariant)
                            <p class="mb-1"><small class="text-muted">Biến thể: {{ $item->productVariant->variant_name }}</small></p>
                        @endif
                        <p class="mb-1">Giá: <strong>{{ number_format($item->price, 2) }}₫</strong></p>
                    </div>

                    <!-- Quantity -->
                    <div class="me-3">
                        <form action="{{ route('cart.update', $item) }}" method="POST" class="d-flex align-items-center">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock_quantity }}" class="form-control form-control-sm me-2" style="width: 80px;">
                            <button type="submit" class="btn btn-outline-primary btn-sm">Cập nhật</button>
                        </form>
                    </div>

                    <!-- Total -->
                    <div class="me-3">
                        <strong>${{ number_format($item->price * $item->quantity, 2) }}</strong>
                    </div>

                    <!-- Remove -->
                    <div>
                        <form action="{{ route('cart.remove', $item) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-trash me-1"></i> Xóa
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Cart Summary -->
        <div class="mt-4 p-4 border rounded bg-light">
            <h4 class="mb-3">Tổng cộng: <span class="text-primary">{{ number_format($total, 2) }}₫</span></h4>
            <div class="d-flex gap-3">
                <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa tất cả sản phẩm?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="fas fa-trash me-1"></i> Xóa tất cả
                    </button>
                </form>
                <a href="{{ route('checkout.index') }}" class="btn btn-success">
                    <i class="fas fa-credit-card me-1"></i> Đặt hàng
                </a>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fs-1 text-muted mb-3"></i>
            <h3 class="mb-2">Giỏ hàng trống</h3>
            <p class="text-muted">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
            <a href="{{ route('home') }}" class="btn btn-primary mt-3">Tiếp tục mua sắm</a>
        </div>
    @endif
</div>
@endsection
