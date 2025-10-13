@extends('layouts.admin')

@section('title', 'Chi tiết người dùng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mt-4">Chi tiết người dùng</h1>
    <div>
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning me-2">
            <i class="fas fa-edit me-2"></i>Chỉnh sửa
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>
</div>

<div class="row">
    <!-- User Info -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user me-1"></i>
                Thông tin cá nhân
            </div>
            <div class="card-body text-center">
                <div class="avatar-lg mx-auto mb-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-center" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>
                
                <h5 class="card-title">{{ $user->name }}</h5>
                
                @if($user->role === 'admin')
                    <span class="badge bg-danger mb-3">
                        <i class="fas fa-crown me-1"></i>Quản trị viên
                    </span>
                @else
                    <span class="badge bg-primary mb-3">
                        <i class="fas fa-user me-1"></i>Khách hàng
                    </span>
                @endif

                @if($user->id === auth()->id())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Đây là tài khoản của bạn
                    </div>
                @endif

                <div class="text-start">
                    <p class="mb-2">
                        <strong><i class="fas fa-envelope me-2 text-muted"></i>Email:</strong><br>
                        <span class="text-muted">{{ $user->email }}</span>
                    </p>
                    
                    <p class="mb-2">
                        <strong><i class="fas fa-phone me-2 text-muted"></i>Số điện thoại:</strong><br>
                        <span class="text-muted">{{ $user->phone ?? 'Chưa cập nhật' }}</span>
                    </p>
                    
                    <p class="mb-2">
                        <strong><i class="fas fa-calendar me-2 text-muted"></i>Ngày tham gia:</strong><br>
                        <span class="text-muted">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </p>
                    
                    <p class="mb-0">
                        <strong><i class="fas fa-clock me-2 text-muted"></i>Cập nhật cuối:</strong><br>
                        <span class="text-muted">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Address Card -->
        @if($user->address)
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-map-marker-alt me-1"></i>
                Địa chỉ
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $user->address }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Orders and Statistics -->
    <div class="col-lg-8">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Tổng đơn hàng</h6>
                                <h3 class="mb-0">{{ $user->orders->count() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-shopping-cart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Đã hoàn thành</h6>
                                <h3 class="mb-0">{{ $user->orders->where('status', 'delivered')->count() }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title">Tổng chi tiêu</h6>
                                <h3 class="mb-0">${{ number_format($user->orders->where('status', 'delivered')->sum('total_amount'), 2) }}</h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-list me-1"></i>
                    Đơn hàng gần đây
                </div>
                @if($user->orders->count() > 5)
                    <a href="{{ route('admin.orders.index', ['user' => $user->id]) }}" class="btn btn-sm btn-outline-primary">
                        Xem tất cả
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if($user->orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->orders->take(5) as $order)
                                    <tr>
                                        <td>
                                            <strong>#{{ $order->order_id }}</strong>
                                        </td>
                                        <td>{{ $order->order_date->format('d/m/Y') }}</td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            @php
                                                $statusClasses = [
                                                    'pending' => 'bg-warning',
                                                    'processing' => 'bg-info',
                                                    'shipped' => 'bg-primary',
                                                    'delivered' => 'bg-success',
                                                    'cancelled' => 'bg-danger'
                                                ];
                                                $statusTexts = [
                                                    'pending' => 'Chờ xử lý',
                                                    'processing' => 'Đang xử lý',
                                                    'shipped' => 'Đã gửi',
                                                    'delivered' => 'Đã giao',
                                                    'cancelled' => 'Đã hủy'
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusClasses[$order->status] ?? 'bg-secondary' }}">
                                                {{ $statusTexts[$order->status] ?? ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Người dùng chưa có đơn hàng nào</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection