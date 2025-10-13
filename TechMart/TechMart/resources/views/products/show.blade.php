@extends('layouts.app')

@section('content')
<div class="bg-white">
    <div class="pt-4">
        <!-- Product info -->
        <div class="container px-4 pb-5 pt-4">
            <div class="row gx-5">
                <div class="col-lg-8 border-end">
                    <h1 class="display-5 fw-bold">{{ $product->name }}</h1>

                    <div class="my-4 text-center">
                        @if($product->image_url && file_exists(public_path('storage/' . $product->image_url)))
                            <img src="{{ asset('storage/' . $product->image_url) }}" 
                                alt="{{ $product->name }}" 
                                class="img-fluid rounded" 
                                style="max-height: 400px; object-fit: contain;">
                        @else
                            <div class="text-center py-5 bg-light">
                                <i class="fas fa-image text-muted fs-1"></i>
                            </div>
                        @endif
                    </div>
                </div>


                <!-- Options -->
                <div class="col-lg-4">
                    <h2 class="visually-hidden">Product information</h2>
                    <p class="fs-2 fw-semibold">{{ number_format($product->price, 2) }}₫</p>

                    <!-- Stock info -->
                    <div class="mt-3">
                        <h3 class="h6 fw-medium">Stock</h3>
                        <p class="text-muted">
                            @if($product->stock_quantity > 0)
                                <span class="text-success">In Stock ({{ $product->stock_quantity }} available)</span>
                            @else
                                <span class="text-danger">Out of Stock</span>
                            @endif
                        </p>
                    </div>

                    <!-- Add to cart form -->
                    <form action="{{ route('cart.store') }}" method="POST" class="mt-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->product_id }}">

                        <!-- Quantity selector -->
                        <div class="mb-3">
                            <label for="quantity" class="form-label fw-medium">Quantity</label>
                            <select name="quantity" id="quantity" class="form-select" aria-label="Select quantity">
                                @for($i = 1; $i <= min(10, $product->stock_quantity); $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        <!-- Variants -->
                        @if($product->variants->count() > 0)
                            <div class="mb-3">
                                <label class="form-label fw-medium">Variants</label>
                                <div class="row row-cols-1 row-cols-sm-2 g-3">
                                    @foreach($product->variants as $variant)
                                        <div class="col">
                                            <div class="form-check border rounded p-3 d-flex align-items-start cursor-pointer" style="cursor:pointer;">
                                                <input class="form-check-input mt-1" type="radio" name="variant_id" id="variant-{{ $variant->variant_id }}" value="{{ $variant->variant_id }}">
                                                <label class="form-check-label ms-2" for="variant-{{ $variant->variant_id }}">
                                                    <div class="fw-semibold">{{ $variant->variant_name }}</div>
                                                    <small class="text-muted">
                                                        @if($variant->additional_price > 0)
                                                            +{{ number_format($variant->additional_price, 2) }}₫
                                                        @else
                                                            No additional cost
                                                        @endif
                                                    </small>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Nút Add to Cart -->
                        <button type="submit" 
                                id="add-to-cart-btn"
                                class="btn btn-primary w-100 mt-4"
                                @if($product->stock_quantity <= 0) disabled @endif
                                @if($product->variants->count() > 0) disabled @endif
                        >
                            @if($product->stock_quantity > 0)
                                Add to Cart
                            @else
                                Out of Stock
                            @endif
                        </button>
                    </form>
                </div>
            </div>

            <div class="row gx-5 mt-4">
                <div class="col-lg-8 border-end">
                    <!-- Description and details -->
                    <h3 class="visually-hidden">Description</h3>
                    <p class="text-secondary">{{ $product->description }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="position-fixed top-0 end-0 m-3 alert alert-success alert-dismissible fade show" role="alert" style="z-index: 1050;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="position-fixed top-0 end-0 m-3 alert alert-danger alert-dismissible fade show" role="alert" style="z-index: 1050;">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('input[name="variant_id"]');
    
    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            radios.forEach(r => {
                r.closest('.form-check').classList.remove('border-primary', 'shadow');
            });
            if(radio.checked) {
                radio.closest('.form-check').classList.add('border-primary', 'shadow');
            }
        });
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('input[name="variant_id"]');
    const addToCartBtn = document.getElementById('add-to-cart-btn');

    // Nếu không có biến thể, nút được bật sẵn
    if (radios.length === 0) {
        addToCartBtn.disabled = false;
    } else {
        addToCartBtn.disabled = true; // disable lúc đầu nếu có biến thể

        radios.forEach(radio => {
            radio.addEventListener('change', () => {
                // Bật nút khi chọn biến thể
                if (radio.checked) {
                    addToCartBtn.disabled = false;
                }

                // Hiệu ứng border cho biến thể đã chọn (bạn có rồi)
                radios.forEach(r => {
                    r.closest('.form-check').classList.remove('border-primary', 'shadow');
                });
                if(radio.checked) {
                    radio.closest('.form-check').classList.add('border-primary', 'shadow');
                }
            });
        });
    }
});
</script>
@endsection
