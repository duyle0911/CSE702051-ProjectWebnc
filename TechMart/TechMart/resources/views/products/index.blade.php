@extends('layouts.app')

@section('content')
<div class="row">
    @foreach($products as $product)
        <div class="col-6 col-md-4 col-lg-3 mb-4">
            <div class="card h-100 shadow-sm">
                <a href="{{ route('products.show', $product) }}">
                    @if($product->image_url)
                        <img src="{{ asset('storage/' . $product->image_url) }}" class="card-img-top" alt="{{ $product->name }}"style="aspect-ratio: 1 / 1; width: 100%; object-fit: cover;">
                    @else
                        <div class="text-center py-5 bg-light">
                            <i class="fas fa-image text-muted fs-1"></i>
                        </div>
                    @endif
                </a>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fs-6">
                        <a href="{{ route('products.show', $product) }}" class="text-decoration-none text-dark">
                            {{ $product->name }}
                        </a>
                    </h5>
                    <p class="text-danger fw-semibold">{{ number_format($product->price, 2) }}₫</p>
                    <p class="text-muted small mb-2">Kho: {{ $product->stock_quantity }}</p>
                                    @if($product->stock_quantity > 0)
                                        <button onclick="addToCart({{ $product->product_id }})" class="btn btn-danger btn-sm w-100 add-to-cart-btn" data-product-id="{{ $product->product_id }}">
                                            <i class="fas fa-cart-plus me-2"></i>Thêm vào giỏ
                                        </button>
                    @else
                        <button class="btn btn-secondary mt-auto w-100" disabled>Hết hàng</button>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>



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
