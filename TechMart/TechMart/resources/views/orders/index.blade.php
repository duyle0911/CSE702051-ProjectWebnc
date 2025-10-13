@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-shopping-bag text-primary me-2"></i>
            Đơn hàng của tôi
        </h1>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row mb-4">
        <div class="col-md-2 col-6 mb-2">
            <div class="card text-center h-100">
                <div class="card-body py-3">
                    <div class="text-primary">
                        <i class="fas fa-list-alt fa-2x mb-2"></i>
                    </div>
                    <h5 class="mb-1">{{ $orders->total() }}</h5>
                    <small class="text-muted">Tổng đơn</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-2">
            <div class="card text-center h-100">
                <div class="card-body py-3">
                    <div class="text-warning">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                    </div>
                    <h5 class="mb-1">{{ $statusCounts['pending'] ?? 0 }}</h5>
                    <small class="text-muted">Chờ xử lý</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-2">
            <div class="card text-center h-100">
                <div class="card-body py-3">
                    <div class="text-info">
                        <i class="fas fa-cog fa-2x mb-2"></i>
                    </div>
                    <h5 class="mb-1">{{ $statusCounts['processing'] ?? 0 }}</h5>
                    <small class="text-muted">Đang xử lý</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-2">
            <div class="card text-center h-100">
                <div class="card-body py-3">
                    <div class="text-primary">
                        <i class="fas fa-shipping-fast fa-2x mb-2"></i>
                    </div>
                    <h5 class="mb-1">{{ $statusCounts['shipped'] ?? 0 }}</h5>
                    <small class="text-muted">Đang giao</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-2">
            <div class="card text-center h-100">
                <div class="card-body py-3">
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                    </div>
                    <h5 class="mb-1">{{ ($statusCounts['delivered'] ?? 0) + ($statusCounts['completed'] ?? 0) }}</h5>
                    <small class="text-muted">Hoàn thành</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-6 mb-2">
            <div class="card text-center h-100">
                <div class="card-body py-3">
                    <div class="text-danger">
                        <i class="fas fa-times-circle fa-2x mb-2"></i>
                    </div>
                    <h5 class="mb-1">{{ $statusCounts['cancelled'] ?? 0 }}</h5>
                    <small class="text-muted">Đã hủy</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('orders.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           class="form-control" placeholder="Mã đơn hàng...">
                </div>
                <div class="col-md-4">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                            Chờ xử lý
                        </option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>
                            Đang xử lý
                        </option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>
                            Đang giao hàng
                        </option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>
                            Đã giao hàng
                        </option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                            Hoàn thành
                        </option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                            Đã hủy
                        </option>
                    </select>
                </div>
                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách đơn hàng -->
    @if($orders->count() > 0)
        <div class="row">
            @foreach($orders as $order)
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">
                                    <strong>
                                        @if($order->order_number)
                                            {{ $order->order_number }}
                                        @else
                                            #{{ $order->order_id }}
                                        @endif
                                    </strong>
                                </h6>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $order->order_date->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            <div class="text-end">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-warning text-dark',
                                        'processing' => 'bg-info',
                                        'shipped' => 'bg-primary',
                                        'delivered' => 'bg-success',
                                        'completed' => 'bg-success',
                                        'cancelled' => 'bg-danger'
                                    ];
                                    $statusTexts = [
                                        'pending' => 'Chờ xử lý',
                                        'processing' => 'Đang xử lý',
                                        'shipped' => 'Đang giao hàng',
                                        'delivered' => 'Đã giao hàng',
                                        'completed' => 'Hoàn thành',
                                        'cancelled' => 'Đã hủy'
                                    ];
                                @endphp
                                <span class="badge {{ $statusClasses[$order->status] ?? 'bg-secondary' }} fs-6">
                                    {{ $statusTexts[$order->status] ?? ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <!-- Sản phẩm trong đơn hàng -->
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-2">Sản phẩm đã đặt:</h6>
                                        @foreach($order->orderItems->take(3) as $item)
                                            <div class="d-flex align-items-center mb-2">
                                                @if($item->product && $item->product->image_url)
                                                    <img src="{{ asset('storage/' . $item->product->image_url) }}" 
                                                         alt="{{ $item->product->name }}" 
                                                         class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold">
                                                        {{ $item->product_name ?? ($item->product->name ?? 'Sản phẩm không xác định') }}
                                                    </div>
                                                    @if($item->variant_name && $item->variant_name !== 'Mặc định')
                                                        <small class="text-muted">{{ $item->variant_name }}</small>
                                                    @endif
                                                    <div class="text-muted">
                                                        Số lượng: {{ $item->quantity }} × {{ number_format($item->price, 0, ',', '.') }}₫
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($order->orderItems->count() > 3)
                                            <small class="text-muted">
                                                +{{ $order->orderItems->count() - 3 }} sản phẩm khác
                                            </small>
                                        @endif
                                    </div>

                                    <!-- Thông tin giao hàng -->
                                    @if($order->shipping_name)
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-truck me-1"></i>
                                                Giao đến: {{ $order->shipping_name }} - {{ $order->shipping_phone }}
                                            </small>
                                        </div>
                                    @endif

                                    <!-- Phương thức thanh toán -->
                                    @if($order->payment_method)
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-credit-card me-1"></i>
                                                Thanh toán: 
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
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="mb-2">
                                        <h5 class="text-primary mb-1">
                                            {{ number_format($order->total_amount, 0, ',', '.') }}₫
                                        </h5>
                                        <small class="text-muted">{{ $order->orderItems->count() }} sản phẩm</small>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i> Xem chi tiết
                                        </a>
                                        @if($order->status === 'pending')
                                            <button class="btn btn-outline-danger btn-sm cancel-order-btn" 
                                                    data-order-id="{{ $order->order_id }}"
                                                    data-order-number="{{ $order->order_number ?? '#' . $order->order_id }}">
                                                <i class="fas fa-times me-1"></i> Hủy đơn
                                            </button>
                                        @endif
                                        @if(in_array($order->status, ['delivered', 'completed']))
                                            <button class="btn btn-outline-success btn-sm reorder-btn" 
                                                    data-order-id="{{ $order->order_id }}"
                                                    data-order-number="{{ $order->order_number ?? '#' . $order->order_id }}">
                                                <i class="fas fa-redo me-1"></i> Mua lại
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="d-flex justify-content-center">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <!-- Empty state -->
        <div class="text-center py-5">
            <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
            <h4 class="text-muted mb-2">Chưa có đơn hàng nào</h4>
            <p class="text-secondary mb-4">
                @if(request('status') || request('search'))
                    Không tìm thấy đơn hàng phù hợp với bộ lọc của bạn.
                @else
                    Bạn chưa đặt đơn hàng nào. Hãy bắt đầu mua sắm ngay!
                @endif
            </p>
            @if(request('status') || request('search'))
                <a href="{{ route('orders.index') }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-times me-1"></i> Xóa bộ lọc
                </a>
            @endif
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="fas fa-shopping-cart me-1"></i> Tiếp tục mua sắm
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Thêm CSRF token vào meta tag nếu chưa có
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.getElementsByTagName('head')[0].appendChild(meta);
    }

    // Xử lý sự kiện hủy đơn hàng
    document.querySelectorAll('.cancel-order-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const orderNumber = this.getAttribute('data-order-number');
            
            if (confirm(`Bạn có chắc chắn muốn hủy đơn hàng ${orderNumber}?`)) {
                cancelOrder(orderId, this);
            }
        });
    });

    // Xử lý sự kiện mua lại
    document.querySelectorAll('.reorder-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const orderNumber = this.getAttribute('data-order-number');
            
            if (confirm(`Bạn có muốn thêm tất cả sản phẩm từ đơn hàng ${orderNumber} vào giỏ hàng?`)) {
                reorderItems(orderId, this);
            }
        });
    });
});

function cancelOrder(orderId, buttonElement) {
    // Disable button để tránh click nhiều lần
    buttonElement.disabled = true;
    buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang hủy...';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/orders/${orderId}/cancel`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Hiển thị thông báo thành công
            alert('Đơn hàng đã được hủy thành công!');
            // Reload trang để cập nhật trạng thái
            location.reload();
        } else {
            // Hiển thị thông báo lỗi
            alert(data.message || 'Không thể hủy đơn hàng. Vui lòng thử lại.');
            // Khôi phục button
            buttonElement.disabled = false;
            buttonElement.innerHTML = '<i class="fas fa-times me-1"></i> Hủy đơn';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi khi hủy đơn hàng. Vui lòng thử lại.');
        // Khôi phục button
        buttonElement.disabled = false;
        buttonElement.innerHTML = '<i class="fas fa-times me-1"></i> Hủy đơn';
    });
}

function reorderItems(orderId, buttonElement) {
    // Disable button để tránh click nhiều lần
    buttonElement.disabled = true;
    buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Đang xử lý...';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/orders/${orderId}/reorder`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Hiển thị thông báo thành công
            alert(data.message);
            
            // Hỏi người dùng có muốn đi đến giỏ hàng không
            if (data.added_items > 0 && confirm('Đã thêm sản phẩm vào giỏ hàng. Bạn có muốn xem giỏ hàng không?')) {
                window.location.href = data.cart_url;
            } else {
                // Khôi phục button
                buttonElement.disabled = false;
                buttonElement.innerHTML = '<i class="fas fa-redo me-1"></i> Mua lại';
            }
        } else {
            // Hiển thị thông báo lỗi
            alert(data.message || 'Không thể mua lại đơn hàng. Vui lòng thử lại.');
            // Khôi phục button
            buttonElement.disabled = false;
            buttonElement.innerHTML = '<i class="fas fa-redo me-1"></i> Mua lại';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi khi mua lại. Vui lòng thử lại.');
        // Khôi phục button
        buttonElement.disabled = false;
        buttonElement.innerHTML = '<i class="fas fa-redo me-1"></i> Mua lại';
    });
}
</script>
@endpush
@endsection
