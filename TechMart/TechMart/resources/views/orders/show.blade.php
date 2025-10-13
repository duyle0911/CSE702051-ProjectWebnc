@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                Chi tiết đơn hàng 
                @if($order->order_number)
                    {{ $order->order_number }}
                @else
                    #{{ $order->order_id }}
                @endif
            </h1>
            <small class="text-muted">
                <i class="fas fa-calendar me-1"></i>
                Đặt ngày {{ $order->order_date->format('d/m/Y H:i') }}
            </small>
        </div>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="row">
        <!-- Order Status Timeline -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-route me-2"></i>Trạng thái đơn hàng
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $statuses = [
                            'pending' => ['label' => 'Chờ xử lý', 'icon' => 'clock', 'color' => 'warning'],
                            'processing' => ['label' => 'Đang xử lý', 'icon' => 'cog', 'color' => 'info'],
                            'shipped' => ['label' => 'Đang giao hàng', 'icon' => 'shipping-fast', 'color' => 'primary'],
                            'delivered' => ['label' => 'Đã giao hàng', 'icon' => 'check', 'color' => 'success'],
                            'completed' => ['label' => 'Hoàn thành', 'icon' => 'star', 'color' => 'success'],
                        ];
                        
                        $currentStatusIndex = array_search($order->status, array_keys($statuses));
                        $isCancelled = $order->status === 'cancelled';
                    @endphp
                    
                    @if($isCancelled)
                        <div class="text-center">
                            <div class="text-danger mb-3">
                                <i class="fas fa-times-circle fa-3x"></i>
                            </div>
                            <h5 class="text-danger">Đơn hàng đã bị hủy</h5>
                            <p class="text-muted">Đơn hàng này đã được hủy và không thể tiếp tục xử lý.</p>
                        </div>
                    @else
                        <div class="d-flex justify-content-between align-items-center position-relative">
                            <!-- Progress line -->
                            <div class="position-absolute w-100" style="height: 2px; background: #dee2e6; top: 50%; z-index: 1;"></div>
                            <div class="position-absolute" style="height: 2px; background: #28a745; top: 50%; z-index: 2; width: {{ ($currentStatusIndex !== false ? ($currentStatusIndex + 1) / count($statuses) * 100 : 0) }}%;"></div>
                            
                            @foreach($statuses as $statusKey => $statusConfig)
                                @php
                                    $statusIndex = array_search($statusKey, array_keys($statuses));
                                    $isActive = $statusIndex <= $currentStatusIndex;
                                    $isCurrent = $statusKey === $order->status;
                                @endphp
                                
                                <div class="text-center position-relative" style="z-index: 3; background: white; padding: 0 15px;">
                                    <div class="mb-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto
                                                    {{ $isActive ? 'bg-' . $statusConfig['color'] . ' text-white' : 'bg-light text-muted' }}"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-{{ $statusConfig['icon'] }} fa-lg"></i>
                                        </div>
                                    </div>
                                    <small class="fw-bold {{ $isActive ? 'text-' . $statusConfig['color'] : 'text-muted' }}">
                                        {{ $statusConfig['label'] }}
                                    </small>
                                    @if($isCurrent)
                                        <div class="mt-1">
                                            <span class="badge bg-{{ $statusConfig['color'] }}">Hiện tại</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="col-lg-8">
            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>Sản phẩm đã đặt ({{ $order->orderItems->count() }} sản phẩm)
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($order->orderItems as $item)
                        <div class="d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            @if($item->product && $item->product->image_url)
                                <img src="{{ asset('storage/' . $item->product->image_url) }}"
                                     alt="{{ $item->product->name }}"
                                     class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                     style="width: 80px; height: 80px;">
                                    <i class="fas fa-image text-muted fa-2x"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    {{ $item->product_name ?? ($item->product->name ?? 'Sản phẩm không xác định') }}
                                </h6>
                                @if($item->variant_name && $item->variant_name !== 'Mặc định')
                                    <p class="mb-1">
                                        <small class="text-muted">Phiên bản: {{ $item->variant_name }}</small>
                                    </p>
                                @endif
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">
                                        {{ number_format($item->price, 0, ',', '.') }}₫ × {{ $item->quantity }}
                                    </span>
                                    <strong class="text-primary">
                                        {{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫
                                    </strong>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-truck me-2"></i>Thông tin giao hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Người nhận:</strong> {{ $order->shipping_name }}</p>
                            <p class="mb-2"><strong>Số điện thoại:</strong> {{ $order->shipping_phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Địa chỉ:</strong></p>
                            <p class="text-muted mb-0">
                                {{ $order->shipping_address }}<br>
                                {{ $order->shipping_ward }}, {{ $order->shipping_district }}, {{ $order->shipping_city }}
                            </p>
                        </div>
                    </div>
                    @if($order->notes)
                        <hr>
                        <p class="mb-0"><strong>Ghi chú:</strong> {{ $order->notes }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt me-2"></i>Tóm tắt đơn hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính:</span>
                        <span>{{ number_format($order->subtotal, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển:</span>
                        <span>{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Thuế VAT:</span>
                        <span>{{ number_format($order->tax_amount, 0, ',', '.') }}₫</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Tổng cộng:</strong>
                        <strong class="text-primary">{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>Thanh toán
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Phương thức:</strong><br>
                        <span class="text-muted">{{ $order->payment_method_label }}</span>
                    </p>
                    <p class="mb-0">
                        <strong>Trạng thái:</strong><br>
                        @php
                            $paymentClasses = [
                                'pending' => 'bg-warning text-dark',
                                'paid' => 'bg-success',
                                'failed' => 'bg-danger',
                                'refunded' => 'bg-info',
                            ];
                            $paymentClass = $paymentClasses[$order->payment_status] ?? 'bg-secondary';
                        @endphp
                        <span class="badge {{ $paymentClass }}">
                            {{ $order->payment_status_label }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>Thao tác
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($order->status === 'pending')
                            <button class="btn btn-danger cancel-order-btn" 
                                    data-order-id="{{ $order->order_id }}"
                                    data-order-number="{{ $order->order_number ?? '#' . $order->order_id }}">
                                <i class="fas fa-times me-2"></i>Hủy đơn hàng
                            </button>
                        @endif
                        
                        @if(in_array($order->status, ['delivered', 'completed']))
                            <button class="btn btn-success reorder-btn" 
                                    data-order-id="{{ $order->order_id }}"
                                    data-order-number="{{ $order->order_number ?? '#' . $order->order_id }}">
                                <i class="fas fa-redo me-2"></i>Mua lại
                            </button>
                        @endif
                        
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>Xem tất cả đơn hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    const cancelButton = document.querySelector('.cancel-order-btn');
    if (cancelButton) {
        cancelButton.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const orderNumber = this.getAttribute('data-order-number');
            
            if (confirm(`Bạn có chắc chắn muốn hủy đơn hàng ${orderNumber}?`)) {
                cancelOrder(orderId, this);
            }
        });
    }

    // Xử lý sự kiện mua lại
    const reorderButton = document.querySelector('.reorder-btn');
    if (reorderButton) {
        reorderButton.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const orderNumber = this.getAttribute('data-order-number');
            
            if (confirm(`Bạn có muốn thêm tất cả sản phẩm từ đơn hàng ${orderNumber} vào giỏ hàng?`)) {
                reorderItems(orderId, this);
            }
        });
    }
});

function cancelOrder(orderId, buttonElement) {
    // Disable button để tránh click nhiều lần
    buttonElement.disabled = true;
    buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang hủy...';
    
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
            alert('Đơn hàng đã được hủy thành công!');
            location.reload();
        } else {
            alert(data.message || 'Không thể hủy đơn hàng. Vui lòng thử lại.');
            buttonElement.disabled = false;
            buttonElement.innerHTML = '<i class="fas fa-times me-2"></i>Hủy đơn hàng';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi khi hủy đơn hàng. Vui lòng thử lại.');
        buttonElement.disabled = false;
        buttonElement.innerHTML = '<i class="fas fa-times me-2"></i>Hủy đơn hàng';
    });
}

function reorderItems(orderId, buttonElement) {
    // Disable button để tránh click nhiều lần
    buttonElement.disabled = true;
    buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
    
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
                buttonElement.innerHTML = '<i class="fas fa-redo me-2"></i>Mua lại';
            }
        } else {
            alert(data.message || 'Không thể mua lại đơn hàng. Vui lòng thử lại.');
            buttonElement.disabled = false;
            buttonElement.innerHTML = '<i class="fas fa-redo me-2"></i>Mua lại';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi khi mua lại. Vui lòng thử lại.');
        buttonElement.disabled = false;
        buttonElement.innerHTML = '<i class="fas fa-redo me-2"></i>Mua lại';
    });
}
</script>
@endpush
@endsection
