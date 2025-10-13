@extends('layouts.app')

@section('title', 'Kết quả tìm kiếm: ' . $query)

@section('content')
<div class="container py-4">
    <!-- Search Header -->
    <div class="mb-4">
        <h1 class="h4 fw-bold text-dark mb-2">
            Kết quả tìm kiếm cho: "<span class="text-danger">{{ $query }}</span>"
        </h1>
        <p class="text-secondary">Tìm thấy {{ $products->total() }} sản phẩm</p>
    </div>

    @if($products->count() > 0)
        <!-- Products Grid -->
        <div class="row g-3 mb-4">
            @foreach($products as $product)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <a href="{{ route('products.show', $product) }}" class="d-block overflow-hidden" style="aspect-ratio: 1 / 1;">
                            @if($product->image_url)
                                <img src="{{ asset('storage/' . $product->image_url) }}" 
                                     alt="{{ $product->name }}" 
                                     class="card-img-top object-fit-cover transition-scale" 
                                     style="transition: transform 0.3s;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100%;">
                                    <i class="fas fa-image text-muted fs-1"></i>
                                </div>
                            @endif
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fs-6 text-truncate">
                                <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                                    {{ $product->name }}
                                </a>
                            </h5>
                            <div class="text-muted small mb-1 text-truncate">
                                {{ $product->category->category_name ?? 'Chưa phân loại' }}
                            </div>
                            <div class="text-danger fw-bold fs-5 mb-2">
                                {{ number_format($product->price, 2) }}₫
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3 small text-muted">
                                <span>Còn {{ $product->stock_quantity }} sản phẩm</span>
                                @if($product->stock_quantity > 0)
                                    <span class="text-success fw-semibold">
                                        <i class="fas fa-check-circle me-1"></i>Còn hàng
                                    </span>
                                @else
                                    <span class="text-danger fw-semibold">
                                        <i class="fas fa-times-circle me-1"></i>Hết hàng
                                    </span>
                                @endif
                            </div>
                            @if($product->stock_quantity > 0)
                                <button onclick="addToCart({{ $product->product_id }})" class="btn btn-danger btn-sm w-100 add-to-cart-btn" data-product-id="{{ $product->product_id }}">
                                    <i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ
                                </button>
                            @else
                                <button class="btn btn-secondary w-100 mt-auto" disabled>Hết hàng</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="d-flex justify-content-center">
                {{ $products->withQueryString()->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-5">
            <i class="fas fa-search fa-5x text-muted mb-3"></i>
            <h3 class="mb-2 text-secondary">Không tìm thấy sản phẩm nào</h3>
            <p class="mb-4 text-secondary">Hãy thử tìm kiếm với từ khóa khác hoặc xem các danh mục sản phẩm</p>
            <a href="{{ route('home') }}" class="btn btn-danger px-4 py-2">
                Về trang chủ
            </a>
        </div>
    @endif
</div>



@push('styles')
<style>
.object-fit-cover {
    object-fit: cover;
}

.text-truncate {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.transition-scale:hover {
    transform: scale(1.05);
    transition: transform 0.3s;
}
</style>
@endpush

@endsection



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
@endauth

@auth
<script>
function addToCart(productId) {
    const button = document.querySelector(`[data-product-id="${productId}"]`);
    const originalText = button.innerHTML;
    
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang thêm...';

    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', 1);
    formData.append('_token', '{{ csrf_token() }}');

    fetch('{{ route("cart.store") }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            updateCartCount();
        } else {
            showToast(data.message || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
        showToast('Không thể thêm sản phẩm vào giỏ', 'error');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('cartToast');
    const toastMessage = document.getElementById('toastMessage');
    const toastHeader = toast.querySelector('.toast-header');
    
    toastMessage.textContent = message;

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

    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}

function updateCartCount() {
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            const cartBadge = document.querySelector('.cart-count');
            if (cartBadge) {
                cartBadge.textContent = data.count;
                cartBadge.style.display = data.count > 0 ? 'inline' : 'none';
            }
        })
        .catch(error => console.error('Lỗi khi cập nhật giỏ hàng:', error));
}
</script>
@endauth