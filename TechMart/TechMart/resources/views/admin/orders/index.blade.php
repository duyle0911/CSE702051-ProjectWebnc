@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Danh sách đơn hàng</h3>
                    <div class="card-tools">
                        <form method="GET" action="{{ route('admin.orders.index') }}" class="d-flex gap-2">
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Đã giao vận chuyển</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Đã giao hàng</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                            <input type="text" name="search" class="form-control form-control-sm" 
                                   placeholder="Tìm kiếm..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Mã đơn hàng</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Sản phẩm</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái đơn hàng</th>
                                    <th>Thanh toán</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>
                                            <strong>
                                                @if(isset($order->order_number))
                                                    {{ $order->order_number }}
                                                @else
                                                    #{{ $order->id ?? $order->order_id }}
                                                @endif
                                            </strong>
                                        </td>
                                        <td>
                                            <div>
                                                @if($order->user)
                                                    <div class="fw-bold">{{ $order->user->name }}</div>
                                                    <small class="text-muted">{{ $order->user->email }}</small>
                                                @else
                                                    <div class="fw-bold text-muted">Khách hàng đã xóa</div>
                                                    @if(isset($order->shipping_name))
                                                        <small class="text-muted">{{ $order->shipping_name }}</small>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div>{{ $order->order_date->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $order->order_date->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $order->orderItems->count() }} sản phẩm</span>
                                            @if($order->orderItems->count() > 0)
                                                <div class="small text-muted mt-1">
                                                    @php
                                                        $firstItem = $order->orderItems->first();
                                                    @endphp
                                                    @if($firstItem && $firstItem->product)
                                                        {{ Str::limit($firstItem->product->name, 30) }}
                                                    @elseif($firstItem && isset($firstItem->product_name))
                                                        {{ Str::limit($firstItem->product_name, 30) }}
                                                    @else
                                                        <span class="text-muted">Sản phẩm không xác định</span>
                                                    @endif
                                                    @if($order->orderItems->count() > 1)
                                                        <br><small>+{{ $order->orderItems->count() - 1 }} sản phẩm khác</small>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ number_format($order->total_amount, 0, ',', '.') }}₫</strong>
                                        </td>
                                        <td>
                                            @php
                                                $statusClasses = [
                                                    'pending' => 'bg-warning text-dark',
                                                    'processing' => 'bg-info text-white',
                                                    'shipped' => 'bg-primary text-white',
                                                    'delivered' => 'bg-success text-white',
                                                    'completed' => 'bg-success text-white',
                                                    'cancelled' => 'bg-danger text-white',
                                                ];
                                                $statusClass = $statusClasses[$order->status] ?? 'bg-secondary text-white';
                                            @endphp
                                            <div class="d-flex flex-column gap-1">
                                                <span class="badge {{ $statusClass }} fs-6">
                                                    {{ $order->status_label ?? ucfirst($order->status) }}
                                                </span>
                                                @if($order->status === 'shipped' && isset($order->shipped_at))
                                                    <small class="text-muted">
                                                        Giao: {{ $order->shipped_at->format('d/m H:i') }}
                                                    </small>
                                                @elseif($order->status === 'delivered' && isset($order->delivered_at))
                                                    <small class="text-muted">
                                                        Nhận: {{ $order->delivered_at->format('d/m H:i') }}
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($order->status === 'cancelled')
                                                <span class="badge bg-secondary">Đã hủy</span>
                                                <small class="text-muted d-block">Đơn hàng đã bị hủy</small>
                                            @else
                                                @if(isset($order->payment_status))
                                                    @php
                                                        $paymentClasses = [
                                                            'pending' => 'bg-warning text-dark',
                                                            'paid' => 'bg-success text-white',
                                                            'failed' => 'bg-danger text-white',
                                                            'refunded' => 'bg-info text-white',
                                                        ];
                                                        $paymentClass = $paymentClasses[$order->payment_status] ?? 'bg-secondary text-white';
                                                    @endphp
                                                    <div class="d-flex flex-column gap-1">
                                                        <span class="badge {{ $paymentClass }}">
                                                            {{ $order->payment_status_label ?? ucfirst($order->payment_status) }}
                                                        </span>
                                                        @if(isset($order->payment_method))
                                                            <small class="text-muted">
                                                                {{ $order->payment_method_label ?? $order->payment_method }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="badge bg-secondary">Chưa xác định</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.orders.show', $order->id ?? $order->order_id) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if($order->status !== 'cancelled' && $order->status !== 'delivered' && $order->status !== 'completed')
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                                data-bs-toggle="dropdown" title="Cập nhật trạng thái">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            @if($order->status === 'pending')
                                                                <li>
                                                                    <form method="POST" action="{{ route('admin.orders.update-status', $order->id ?? $order->order_id) }}" class="d-inline">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="status" value="processing">
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="fas fa-cog text-info"></i> Đang xử lý
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                            
                                                            @if(in_array($order->status, ['pending', 'processing']))
                                                                <li>
                                                                    <form method="POST" action="{{ route('admin.orders.update-status', $order->id ?? $order->order_id) }}" class="d-inline">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="status" value="shipped">
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="fas fa-shipping-fast text-primary"></i> Đã giao vận chuyển
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                            
                                                            @if($order->status === 'shipped')
                                                                <li>
                                                                    <form method="POST" action="{{ route('admin.orders.update-status', $order->id ?? $order->order_id) }}" class="d-inline">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="status" value="delivered">
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="fas fa-check text-success"></i> Đã giao hàng
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif

                                                            @if($order->status === 'delivered')
                                                                <li>
                                                                    <form method="POST" action="{{ route('admin.orders.update-status', $order->id ?? $order->order_id) }}" class="d-inline">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="status" value="completed">
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="fas fa-star text-success"></i> Hoàn thành
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                            
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form method="POST" action="{{ route('admin.orders.update-status', $order->id ?? $order->order_id) }}" class="d-inline">
                                                                    @csrf
                                                                    @method('PATCH')
                                                                    <input type="hidden" name="status" value="cancelled">
                                                                    <button type="submit" class="dropdown-item text-danger" 
                                                                            onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                                                                        <i class="fas fa-times"></i> Hủy đơn hàng
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <p>Không có đơn hàng nào</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($orders->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="text-muted">
                                Hiển thị {{ $orders->firstItem() }} - {{ $orders->lastItem() }} 
                                trong tổng số {{ $orders->total() }} đơn hàng
                            </div>
                            <nav aria-label="Phân trang đơn hàng">
                                <ul class="pagination pagination-sm mb-0">
                                    {{-- Previous Page Link --}}
                                    @if ($orders->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                <i class="fas fa-chevron-left"></i>
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $orders->appends(request()->query())->previousPageUrl() }}">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @php
                                        $start = max($orders->currentPage() - 2, 1);
                                        $end = min($start + 4, $orders->lastPage());
                                        $start = max($end - 4, 1);
                                    @endphp

                                    @if($start > 1)
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $orders->appends(request()->query())->url(1) }}">1</a>
                                        </li>
                                        @if($start > 2)
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @endif
                                    @endif

                                    @for ($i = $start; $i <= $end; $i++)
                                        @if ($i == $orders->currentPage())
                                            <li class="page-item active">
                                                <span class="page-link">{{ $i }}</span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $orders->appends(request()->query())->url($i) }}">{{ $i }}</a>
                                            </li>
                                        @endif
                                    @endfor

                                    @if($end < $orders->lastPage())
                                        @if($end < $orders->lastPage() - 1)
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        @endif
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $orders->appends(request()->query())->url($orders->lastPage()) }}">{{ $orders->lastPage() }}</a>
                                        </li>
                                    @endif

                                    {{-- Next Page Link --}}
                                    @if ($orders->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $orders->appends(request()->query())->nextPageUrl() }}">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link">
                                                <i class="fas fa-chevron-right"></i>
                                            </span>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Statistics -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Thống kê trạng thái đơn hàng</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $statusStats = [
                            'pending' => ['label' => 'Chờ xử lý', 'color' => 'warning', 'icon' => 'clock'],
                            'processing' => ['label' => 'Đang xử lý', 'color' => 'info', 'icon' => 'cog'],
                            'shipped' => ['label' => 'Đã giao vận chuyển', 'color' => 'primary', 'icon' => 'shipping-fast'],
                            'delivered' => ['label' => 'Đã giao hàng', 'color' => 'success', 'icon' => 'check'],
                            'completed' => ['label' => 'Hoàn thành', 'color' => 'success', 'icon' => 'star'],
                            'cancelled' => ['label' => 'Đã hủy', 'color' => 'danger', 'icon' => 'times'],
                        ];
                    @endphp
                    
                    @foreach($statusStats as $status => $config)
                        @php
                            $count = \App\Models\Order::where('status', $status)->count();
                        @endphp
                        <div class="col-md-2 col-sm-4 col-6">
                            <div class="card bg-{{ $config['color'] }} text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-{{ $config['icon'] }} fa-2x mb-2"></i>
                                    <h4>{{ $count }}</h4>
                                    <small>{{ $config['label'] }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.pagination-sm .page-link {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

.pagination .page-link {
    color: #6c757d;
    border: 1px solid #dee2e6;
    text-decoration: none;
}

.pagination .page-link:hover {
    color: #0d6efd;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
    cursor: not-allowed;
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh page every 60 seconds for real-time updates
    setTimeout(function() {
        location.reload();
    }, 60000);
    
    // Tooltip for status badges
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
