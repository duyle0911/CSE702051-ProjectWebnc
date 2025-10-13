@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Chi tiết đơn hàng</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                            <li class="breadcrumb-item active">Chi tiết</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Order Information -->
                <div class="col-lg-8">
                    <!-- Order Status Timeline -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Trạng thái đơn hàng</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @php
                                    $statuses = [
                                        'pending' => ['label' => 'Chờ xử lý', 'icon' => 'clock', 'color' => 'warning'],
                                        'processing' => ['label' => 'Đang xử lý', 'icon' => 'cog', 'color' => 'info'],
                                        'shipped' => ['label' => 'Đã giao vận chuyển', 'icon' => 'shipping-fast', 'color' => 'primary'],
                                        'delivered' => ['label' => 'Đã giao hàng', 'icon' => 'check', 'color' => 'success'],
                                        'completed' => ['label' => 'Hoàn thành', 'icon' => 'star', 'color' => 'success'],
                                        'cancelled' => ['label' => 'Đã hủy', 'icon' => 'times', 'color' => 'danger'],
                                    ];
                                    
                                    $currentStatusIndex = array_search($order->status, array_keys($statuses));
                                @endphp
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    @foreach($statuses as $statusKey => $statusConfig)
                                        @php
                                            $statusIndex = array_search($statusKey, array_keys($statuses));
                                            $isActive = $statusIndex <= $currentStatusIndex && $order->status !== 'cancelled';
                                            $isCurrent = $statusKey === $order->status;
                                            $isCancelled = $order->status === 'cancelled' && $statusKey === 'cancelled';
                                        @endphp
                                        
                                        @if($statusKey !== 'cancelled' || $order->status === 'cancelled')
                                            <div class="text-center {{ $isActive || $isCancelled ? 'text-' . $statusConfig['color'] : 'text-muted' }}">
                                                <div class="mb-2">
                                                    <i class="fas fa-{{ $statusConfig['icon'] }} fa-2x 
                                                       {{ $isCurrent || $isCancelled ? 'text-' . $statusConfig['color'] : ($isActive ? 'text-' . $statusConfig['color'] : 'text-muted') }}"></i>
                                                </div>
                                                <small class="fw-bold">{{ $statusConfig['label'] }}</small>
                                                @if($isCurrent || $isCancelled)
                                                    <div class="mt-1">
                                                        <span class="badge bg-{{ $statusConfig['color'] }}">Hiện tại</span>
                                                    </div>
                                                @endif
                                                
                                                @if($statusKey === 'shipped' && isset($order->shipped_at))
                                                    <div class="mt-1">
                                                        <small class="text-muted">{{ $order->shipped_at->format('d/m/Y H:i') }}</small>
                                                    </div>
                                                @elseif($statusKey === 'delivered' && isset($order->delivered_at))
                                                    <div class="mt-1">
                                                        <small class="text-muted">{{ $order->delivered_at->format('d/m/Y H:i') }}</small>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                Đơn hàng 
                                @if(isset($order->order_number))
                                    {{ $order->order_number }}
                                @else
                                    #{{ $order->id ?? $order->order_id }}
                                @endif
                            </h5>
                            <div>
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-warning text-dark',
                                        'processing' => 'bg-info',
                                        'shipped' => 'bg-primary',
                                        'delivered' => 'bg-success',
                                        'completed' => 'bg-success',
                                        'cancelled' => 'bg-danger',
                                    ];
                                    $statusClass = $statusClasses[$order->status] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $statusClass }} fs-6">
                                    {{ $order->status_label ?? ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Thông tin đơn hàng</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Ngày đặt:</strong></td>
                                            <td>{{ $order->order_date->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Trạng thái:</strong></td>
                                            <td>
                                                <span class="badge {{ $statusClass }}">
                                                    {{ $order->status_label ?? ucfirst($order->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if($order->status !== 'cancelled')
                                            @if(isset($order->payment_method))
                                            <tr>
                                                <td><strong>Phương thức thanh toán:</strong></td>
                                                <td>{{ $order->payment_method_label ?? $order->payment_method }}</td>
                                            </tr>
                                            @endif
                                            @if(isset($order->payment_status))
                                            <tr>
                                                <td><strong>Trạng thái thanh toán:</strong></td>
                                                <td>
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
                                                        {{ $order->payment_status_label ?? ucfirst($order->payment_status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endif
                                        @else
                                            <tr>
                                                <td><strong>Trạng thái đơn hàng:</strong></td>
                                                <td>
                                                    <span class="badge bg-danger">Đã hủy</span>
                                                    <div class="mt-1">
                                                        <small class="text-muted">Đơn hàng này đã bị hủy</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        @if(isset($order->notes) && $order->notes)
                                        <tr>
                                            <td><strong>Ghi chú:</strong></td>
                                            <td>{{ $order->notes }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Thông tin khách hàng</h6>
                                    <table class="table table-sm">
                                        @if($order->user)
                                        <tr>
                                            <td><strong>Tên:</strong></td>
                                            <td>{{ $order->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $order->user->email }}</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <td colspan="2" class="text-muted">Thông tin khách hàng không có sẵn</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Sản phẩm đã đặt ({{ $order->orderItems->count() }} sản phẩm)</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Biến thể</th>
                                            <th>Đơn giá</th>
                                            <th>Số lượng</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($order->orderItems as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
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
                                                        <div>
                                                            <div class="fw-bold">
                                                                @if(isset($item->product_name) && $item->product_name)
                                                                    {{ $item->product_name }}
                                                                @elseif($item->product && $item->product->name)
                                                                    {{ $item->product->name }}
                                                                @else
                                                                    <span class="text-muted">Sản phẩm không xác định</span>
                                                                @endif
                                                            </div>
                                                            @if($item->product && isset($item->product->product_id))
                                                                <small class="text-muted">SKU: {{ $item->product->product_id }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if(isset($item->variant_name) && $item->variant_name)
                                                        <span class="badge bg-light text-dark">{{ $item->variant_name }}</span>
                                                    @elseif($item->productVariant && $item->productVariant->name)
                                                        <span class="badge bg-light text-dark">{{ $item->productVariant->name }}</span>
                                                    @else
                                                        <span class="text-muted">Mặc định</span>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($item->price, 0, ',', '.') }}₫</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>
                                                    <strong>
                                                        @if(isset($item->total))
                                                            {{ number_format($item->total, 0, ',', '.') }}₫
                                                        @else
                                                            {{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫
                                                        @endif
                                                    </strong>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">Không có sản phẩm nào</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary & Actions -->
                <div class="col-lg-4">
                    <!-- Shipping Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Thông tin giao hàng</h5>
                        </div>
                        <div class="card-body">
                            @if(isset($order->shipping_name))
                                <p class="mb-1"><strong>{{ $order->shipping_name }}</strong></p>
                                <p class="mb-1">{{ $order->shipping_phone }}</p>
                                <p class="mb-1">{{ $order->shipping_address ?? 'Địa chỉ chưa cập nhật' }}</p>
                                @if(isset($order->shipping_ward))
                                    <p class="mb-0">{{ $order->shipping_ward }}, {{ $order->shipping_district }}, {{ $order->shipping_city }}</p>
                                @endif
                            @else
                                <p class="text-muted">{{ $order->shipping_address ?? 'Thông tin giao hàng chưa đầy đủ' }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Tổng kết đơn hàng</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                @if(isset($order->subtotal))
                                <tr>
                                    <td>Tạm tính:</td>
                                    <td class="text-end">{{ number_format($order->subtotal, 0, ',', '.') }}₫</td>
                                </tr>
                                @endif
                                @if(isset($order->shipping_fee))
                                <tr>
                                    <td>Phí vận chuyển:</td>
                                    <td class="text-end">{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</td>
                                </tr>
                                @endif
                                @if(isset($order->tax_amount))
                                <tr>
                                    <td>Thuế VAT:</td>
                                    <td class="text-end">{{ number_format($order->tax_amount, 0, ',', '.') }}₫</td>
                                </tr>
                                @endif
                                <tr class="table-active">
                                    <td><strong>Tổng cộng:</strong></td>
                                    <td class="text-end"><strong>{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if($order->status !== 'cancelled' && $order->status !== 'completed')
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Cập nhật trạng thái</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if($order->status === 'pending')
                                    <form method="POST" action="{{ route('admin.orders.update-status', $order->id ?? $order->order_id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="processing">
                                        <button type="submit" class="btn btn-info w-100">
                                            <i class="fas fa-cog"></i> Xác nhận đơn hàng
                                        </button>
                                    </form>
                                @endif
                                
                                @if(in_array($order->status, ['pending', 'processing']))
                                    <form method="POST" action="{{ route('admin.orders.update-status', $order->id ?? $order->order_id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="shipped">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-shipping-fast"></i> Giao cho vận chuyển
                                        </button>
                                    </form>
                                @endif
                                
                                @if($order->status === 'shipped')
                                    <form method="POST" action="{{ route('admin.orders.update-status', $order->id ?? $order->order_id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="delivered">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-check"></i> Xác nhận đã giao
                                        </button>
                                    </form>
                                @endif

                                @if($order->status === 'delivered')
                                    <form method="POST" action="{{ route('admin.orders.update-status', $order->id ?? $order->order_id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-star"></i> Hoàn thành đơn hàng
                                        </button>
                                    </form>
                                @endif
                                
                                <hr>
                                <form method="POST" action="{{ route('admin.orders.update-status', $order->id ?? $order->order_id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="btn btn-danger w-100" 
                                            onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                                        <i class="fas fa-times"></i> Hủy đơn hàng
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 2px;
    background: #dee2e6;
    z-index: 1;
}

.timeline .text-center {
    position: relative;
    z-index: 2;
    background: white;
    padding: 0 10px;
}
</style>
@endpush
