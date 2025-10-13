@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')
@section('header', 'Quản lý sản phẩm')

@section('content')
{{-- Flash messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="mb-4 d-flex align-items-center justify-content-between" style="min-height: 60px;">
    <h1 class="h4 mb-0">Quản lý sản phẩm</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary d-flex align-items-center">
        <i class="bi bi-plus-circle me-1"></i> Thêm sản phẩm
    </a>
</div>


<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Tìm kiếm</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Tên sản phẩm...">
            </div>
            <div class="col-md-4">
                <label for="category" class="form-label">Danh mục</label>
                <select name="category" id="category" class="form-select">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}" {{ request('category') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->category_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-filter"></i> Lọc
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá</th>
                    <th>Tồn kho</th>
                    <th>Biến thể</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    @if($product->image_url)
                                        <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->name }}" class="rounded-circle" width="40" height="40">
                                    @else
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $product->name }}</div>
                                    <div class="text-muted small">ID: {{ $product->product_id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>
                            <span class="badge 
                                {{ $product->stock_quantity > 10 ? 'bg-success' : ($product->stock_quantity > 0 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ $product->stock_quantity }}
                            </span>
                        </td>
                        <td>{{ $product->variants->count() }} biến thể</td>
                        <td class="text-end">
                            <a href="{{ route('admin.products.show', $product->product_id) }}" class="btn btn-sm btn-outline-primary">Xem</a>
                            <a href="{{ route('admin.products.edit', $product->product_id) }}" class="btn btn-sm btn-outline-secondary">Sửa</a>
                            <form action="{{ route('admin.products.destroy', $product->product_id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger delete-btn">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Không có sản phẩm nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    @if($products->hasPages())
        <div class="card-footer">
            {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: "Bạn sẽ không thể khôi phục lại sản phẩm này!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
