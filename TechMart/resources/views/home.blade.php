@extends('layouts.app')

@section('title', 'TechMart - Trang chủ')

@section('content')
    <div class="container">
        <!-- Banner -->
        <div class="bg-danger text-white rounded p-5 text-center mb-5">
            <h1 class="display-5 fw-bold">Chào mừng đến với TechMart</h1>
            <p class="lead">Khám phá sản phẩm công nghệ mới nhất với giá siêu tốt</p>
        </div>

        <!-- Danh mục nổi bật -->
        @if ($categoriesWithProducts)
            @foreach ($categoriesWithProducts as $categoryData)
                <div class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h4 text-dark">
                            <i class="fas fa-tag text-danger me-2"></i>{{ $categoryData['category']->category_name }}
                        </h2>
                        <a href="{{ route('products.category', $categoryData['category']) }}"
                            class="text-danger text-decoration-none">
                            Xem tất cả <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>

                    <div class="row g-3">
                        @foreach ($categoryData['products'] as $product)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="card h-100 shadow-sm">
                                    <a href="{{ route('products.show', $product) }}">
                                        @if ($product->image_url)
                                            <img src="{{ asset('storage/' . $product->image_url) }}" class="card-img-top"
                                                alt="{{ $product->name }}"
                                                style="aspect-ratio: 1 / 1; width: 100%; object-fit: cover;">
                                        @else
                                            <div class="text-center py-5 bg-light" style="height: 200px;">
                                                <i class="fas fa-image text-muted fs-1"></i>
                                            </div>
                                        @endif
                                    </a>
                                    <div class="card-body">
                                        <h5 class="card-title fs-6">
                                            <a href="{{ route('products.show', $product) }}"
                                                class="text-decoration-none text-dark">
                                                {{ $product->name }}
                                            </a>
                                        </h5>
                                        <p class="text-danger fw-semibold">
                                            {{ number_format($product->price, 0, ',', '.') }}₫</p>
                                        @if ($product->compare_price && $product->compare_price > $product->price)
                                            <p class="text-muted small text-decoration-line-through mb-1">
                                                {{ number_format($product->compare_price, 0, ',', '.') }}₫</p>
                                        @endif
                                        <p class="text-muted small mb-2">Kho: {{ $product->stock_quantity }}</p>
                                        @auth
                                            @if ($product->stock_quantity > 0)
                                                <button onclick="addToCart({{ $product->product_id }})"
                                                    class="btn btn-danger btn-sm w-100 add-to-cart-btn"
                                                    data-product-id="{{ $product->product_id }}">
                                                    <i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ
                                                </button>
                                            @else
                                                <button class="btn btn-secondary btn-sm w-100" disabled>Hết hàng</button>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-outline-danger btn-sm w-100">
                                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập để mua
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center my-5 py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Chưa có sản phẩm nào</h4>
                <p class="text-secondary">Vui lòng quay lại sau.</p>
            </div>
        @endif
    </div>

    @auth
        <!-- Toast notification -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="cartToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="fas fa-shopping-cart text-success me-2"></i>
                    <strong class="me-auto">Giỏ hàng</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body" id="toastMessage">
                    <!-- Message will be inserted here -->
                </div>
            </div>
        </div>

        <script>
            function addToCart(productId) {
                const button = document.querySelector(`[data-product-id="${productId}"]`);
                const originalText = button.innerHTML;

                // Disable button and show loading
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang thêm...';

                // Create form data
                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('quantity', 1);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route('cart.store') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success toast
                            showToast(data.message, 'success');

                            // Update cart count if navbar exists
                            updateCartCount();
                        } else {
                            // Show error toast
                            showToast(data.message || 'Có lỗi xảy ra', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Có lỗi xảy ra khi thêm sản phẩm', 'error');
                    })
                    .finally(() => {
                        // Re-enable button
                        button.disabled = false;
                        button.innerHTML = originalText;
                    });
            }

            function showToast(message, type = 'success') {
                const toast = document.getElementById('cartToast');
                const toastMessage = document.getElementById('toastMessage');
                const toastHeader = toast.querySelector('.toast-header');

                // Update message
                toastMessage.textContent = message;

                // Update icon and color based on type
                const icon = toastHeader.querySelector('i');
                if (type === 'success') {
                    icon.className = 'fas fa-check-circle text-success me-2';
                    toastHeader.classList.remove('bg-danger');
                    toastHeader.classList.add('bg-success', 'text-white');
                } else {
                    icon.className = 'fas fa-exclamation-circle text-danger me-2';
                    toastHeader.classList.remove('bg-success');
                    toastHeader.classList.add('bg-danger', 'text-white');
                }

                // Show toast
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
            }

            function updateCartCount() {
                fetch('{{ route('cart.count') }}')
                    .then(response => response.json())
                    .then(data => {
                        const cartBadge = document.querySelector('.cart-count');
                        if (cartBadge) {
                            cartBadge.textContent = data.count;
                            if (data.count > 0) {
                                cartBadge.style.display = 'inline';
                            } else {
                                cartBadge.style.display = 'none';
                            }
                        }
                    })
                    .catch(error => console.error('Error updating cart count:', error));
            }
        </script>
    @endauth
@endsection
