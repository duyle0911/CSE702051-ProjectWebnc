@extends('layouts.admin')

@section('title', 'Chi tiết sản phẩm')
@section('header', 'Chi tiết sản phẩm')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4>{{ $product->name }} (ID: {{ $product->product_id }})</h4>
        </div>
        <div class="card-body row">
            <div class="col-md-4">
                @if($product->image_url)
                    <img src="{{ asset('storage/' . $product->image_url) }}" class="img-fluid rounded" alt="{{ $product->name }}">
                @else
                    <div class="bg-light text-center py-5 border rounded">
                        <p class="text-muted">Không có ảnh</p>
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <p><strong>Mô tả:</strong> {{ $product->description ?? 'Không có' }}</p>
                <p><strong>Danh mục:</strong> {{ $product->category->category_name ?? 'Không xác định' }}</p>
                <p><strong>Giá:</strong> ${{ number_format($product->price, 2) }}</p>
                <p><strong>Tồn kho:</strong> {{ $product->stock_quantity }}</p>
                <p><strong>Số lượng biến thể:</strong> {{ $product->variants->count() }}</p>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
            <a href="{{ route('admin.products.edit', $product->product_id) }}" class="btn btn-primary">Chỉnh sửa</a>
        </div>
    </div>
</div>
@endsection
