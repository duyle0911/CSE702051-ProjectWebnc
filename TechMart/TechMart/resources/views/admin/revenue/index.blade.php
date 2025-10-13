@extends('layouts.admin')

@section('title', 'Thống kê doanh thu')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4">
            <i class="fas fa-chart-line me-2"></i>Thống kê doanh thu
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.revenue.export', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-download me-1"></i> Xuất báo cáo
            </a>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.revenue.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="period" class="form-label">Khoảng thời gian</label>
                    <select name="period" id="period" class="form-select">
                        <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Hôm nay</option>
                        <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Tuần này</option>
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Tháng này</option>
                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Năm này</option>
                        <option value="custom" {{ ($startDate && $endDate) ? 'selected' : '' }}>Tùy chọn</option>
                    </select>
                </div>
                <div class="col-md-3" id="start-date-group" style="{{ ($startDate && $endDate) ? '' : 'display: none;' }}">
                    <label for="start_date" class="form-label">Từ ngày</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="form-control">
                </div>
                <div class="col-md-3" id="end-date-group" style="{{ ($startDate && $endDate) ? '' : 'display: none;' }}">
                    <label for="end_date" class="form-label">Đến ngày</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="form-control">
                </div>
                <div class="col-md-3 align-self-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-white-75 small">Doanh thu</div>
                            <div class="text-lg font-weight-bold">{{ number_format($overview['current_revenue'], 0, ',', '.') }}₫</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <span class="small text-white-75">
                        @if($overview['revenue_growth'] > 0)
                            <i class="fas fa-arrow-up"></i> +{{ number_format($overview['revenue_growth'], 1) }}%
                        @elseif($overview['revenue_growth'] < 0)
                            <i class="fas fa-arrow-down"></i> {{ number_format($overview['revenue_growth'], 1) }}%
                        @else
                            <i class="fas fa-minus"></i> 0%
                        @endif
                        so với kỳ trước
                    </span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-white-75 small">Tổng đơn hàng</div>
                            <div class="text-lg font-weight-bold">{{ number_format($overview['total_orders']) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-shopping-cart fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <span class="small text-white-75">{{ number_format($overview['completion_rate'], 1) }}% hoàn thành</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-white-75 small">Đơn hoàn thành</div>
                            <div class="text-lg font-weight-bold">{{ number_format($overview['completed_orders']) }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <span class="small text-white-75">Từ {{ number_format($overview['total_orders']) }} đơn</span>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="text-white-75 small">Giá trị TB/đơn</div>
                            <div class="text-lg font-weight-bold">{{ number_format($overview['avg_order_value'], 0, ',', '.') }}₫</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calculator fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <span class="small text-white-75">Trung bình mỗi đơn</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Biểu đồ doanh thu -->
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-area me-1"></i>
                    Biểu đồ doanh thu theo thời gian
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="width: 100%; height: 400px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Thống kê đơn hàng theo trạng thái -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Đơn hàng theo trạng thái
                </div>
                <div class="card-body">
                    <canvas id="orderStatusChart" style="width: 100%; height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top sản phẩm bán chạy -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-trophy me-1"></i>
                    Top sản phẩm bán chạy
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($product->image_url)
                                                    <img src="{{ asset('storage/' . $product->image_url) }}" 
                                                         alt="{{ $product->name }}" 
                                                         class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                         style="width: 40px; height: 40px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <span>{{ Str::limit($product->name, 30) }}</span>
                                            </div>
                                        </td>
                                        <td>{{ number_format($product->total_quantity) }}</td>
                                        <td>{{ number_format($product->total_revenue, 0, ',', '.') }}₫</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doanh thu theo danh mục -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-tags me-1"></i>
                    Doanh thu theo danh mục
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" style="width: 100%; height: 400px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top khách hàng -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-users me-1"></i>
                    Top khách hàng
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th>Email</th>
                                    <th>Số đơn hàng</th>
                                    <th>Tổng chi tiêu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCustomers as $customer)
                                    <tr>
                                        <td>{{ $customer->name }}</td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ number_format($customer->total_orders) }}</td>
                                        <td>{{ number_format($customer->total_spent, 0, ',', '.') }}₫</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kiểm tra xem Chart.js đã được tải chưa
    if (typeof Chart === 'undefined') {
        console.error('Chart.js chưa được tải!');
        return;
    }

    // Xử lý hiển thị date picker
    const periodSelect = document.getElementById('period');
    const startDateGroup = document.getElementById('start-date-group');
    const endDateGroup = document.getElementById('end-date-group');

    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                startDateGroup.style.display = 'block';
                endDateGroup.style.display = 'block';
            } else {
                startDateGroup.style.display = 'none';
                endDateGroup.style.display = 'none';
            }
        });
    }

    // Dữ liệu từ server
    const revenueData = @json($revenueChart);
    const orderStatsData = @json($orderStats);
    const categoryData = @json($categoryRevenue);

    console.log('Revenue Data:', revenueData);
    console.log('Order Stats:', orderStatsData);
    console.log('Category Data:', categoryData);

    // Biểu đồ doanh thu
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueData.labels || [],
                datasets: [{
                    label: 'Doanh thu (₫)',
                    data: revenueData.revenues || [],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    yAxisID: 'y'
                }, {
                    label: 'Số đơn hàng',
                    data: revenueData.orders || [],
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Thời gian'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Doanh thu (₫)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Số đơn hàng'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
    }

    // Biểu đồ trạng thái đơn hàng
    const orderStatusCtx = document.getElementById('orderStatusChart');
    if (orderStatusCtx && orderStatsData) {
        const statusLabels = [];
        const statusCounts = [];
        const statusColors = [];

        const statusConfig = {
            'pending': { label: 'Chờ xử lý', color: '#ffc107' },
            'processing': { label: 'Đang xử lý', color: '#17a2b8' },
            'shipped': { label: 'Đang giao', color: '#007bff' },
            'delivered': { label: 'Đã giao', color: '#28a745' },
            'completed': { label: 'Hoàn thành', color: '#20c997' },
            'cancelled': { label: 'Đã hủy', color: '#dc3545' }
        };

        Object.keys(orderStatsData).forEach(status => {
            const config = statusConfig[status] || { label: status, color: '#6c757d' };
            statusLabels.push(config.label);
            statusCounts.push(orderStatsData[status].count);
            statusColors.push(config.color);
        });

        new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusCounts,
                    backgroundColor: statusColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Biểu đồ doanh thu theo danh mục
    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx && categoryData && categoryData.length > 0) {
        const categoryLabels = categoryData.map(item => item.category_name);
        const categoryRevenues = categoryData.map(item => parseFloat(item.total_revenue));

        new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Doanh thu (₫)',
                    data: categoryRevenues,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Danh mục'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Doanh thu (₫)'
                        },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + '₫';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + '₫';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
