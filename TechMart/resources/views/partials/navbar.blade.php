<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">

        {{-- Dropdown tất cả danh mục
        <div class="me-3 dropdown">
            <a class="nav-link dropdown-toggle text-danger fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                <i class="fas fa-bars me-1"></i> Tất cả danh mục
            </a>
            <ul class="dropdown-menu">
                @foreach ($categories as $category)
                    <li>
                        <a class="dropdown-item" href="{{ route('products.category', $category) }}">
                            {{ $category->category_name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div> --}}

        <a class="navbar-brand text-danger fw-bold" href="{{ route('home') }}">
            <i class="fas fa-store me-2"></i>TechMart
        </a>

        <form class="d-flex mx-auto" action="{{ route('search') }}" method="GET">
            <input class="form-control me-2" type="search" name="q" placeholder="Tìm kiếm sản phẩm..." value="{{ request('q') }}">
            <button class="btn btn-danger" type="submit"><i class="fas fa-search"></i></button>
        </form>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a href="{{ route('cart.index') }}" class="nav-link position-relative">
                    <i class="fas fa-shopping-cart fs-5 text-dark"></i>
                    @auth
                        @if(auth()->user()->cartItems->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count">
                                {{ auth()->user()->cartItems->sum('quantity') }}
                            </span>
                        @endif
                    @endauth
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user fs-5"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    @auth
                        <li><span class="dropdown-item-text">{{ auth()->user()->name }}</span></li>
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Tài khoản</a></li>
                        <li><a class="dropdown-item" href="{{ route('cart.index') }}">Giỏ hàng</a></li>
                        <li><a class="dropdown-item" href="{{ route('orders.index') }}">Đơn hàng của tôi</a></li>
                        @if(auth()->user()->isAdmin())
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Quản trị</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item" type="submit">Đăng xuất</button>
                            </form>
                        </li>
                    @else
                        <li><a class="dropdown-item" href="{{ route('login') }}">Đăng nhập</a></li>
                        <li><a class="dropdown-item" href="{{ route('register') }}">Đăng ký</a></li>
                    @endauth
                </ul>
            </li>
        </ul>
    </div>
</nav>

@auth
<script>
// Load cart count when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});

function updateCartCount() {
    fetch('{{ route("cart.count") }}')
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

// Make updateCartCount globally available
window.updateCartCount = updateCartCount;
</script>
@endauth
