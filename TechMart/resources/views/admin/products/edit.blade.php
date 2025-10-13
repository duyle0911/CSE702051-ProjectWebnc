@extends('layouts.admin')

@section('title', 'Sửa sản phẩm')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
            <li class="breadcrumb-item active">Sửa sản phẩm</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit me-2 text-primary"></i>Sửa sản phẩm
            </h1>
            <p class="text-muted mb-0">Cập nhật thông tin sản phẩm</p>
        </div>
        <div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Có lỗi xảy ra:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.products.update', $product->product_id) }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Thông tin cơ bản
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold">
                                        Tên sản phẩm <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $product->name) }}"
                                           placeholder="Nhập tên sản phẩm..." 
                                           required>
                                    <div class="form-text">Tên sản phẩm sẽ hiển thị cho khách hàng</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label fw-bold">
                                        Danh mục <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select form-select-lg" id="category_id" name="category_id" required>
                                        <option value="">-- Chọn danh mục --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->category_id }}" 
                                                    {{ old('category_id', $product->category_id) == $category->category_id ? 'selected' : '' }}>
                                                {{ $category->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Mô tả sản phẩm</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="4" 
                                      placeholder="Mô tả chi tiết về sản phẩm...">{{ old('description', $product->description) }}</textarea>
                            <div class="form-text">Mô tả chi tiết giúp khách hàng hiểu rõ hơn về sản phẩm</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label fw-bold">
                                        Giá bán <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <input type="number" 
                                               class="form-control" 
                                               id="price" 
                                               name="price" 
                                               value="{{ old('price', $product->price) }}"
                                               step="0.01" 
                                               min="0" 
                                               placeholder="0.00" 
                                               required>
                                        <span class="input-group-text">₫</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock_quantity" class="form-label fw-bold">
                                        Số lượng tồn kho <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control form-control-lg" 
                                           id="stock_quantity" 
                                           name="stock_quantity" 
                                           value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                           min="0" 
                                           placeholder="0" 
                                           required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Variants -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-layer-group me-2"></i>Biến thể sản phẩm
                        </h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enableVariants" 
                                   {{ old('enable_variants', $product->variants->count() > 0) ? 'checked' : '' }}>
                            <label class="form-check-label text-white" for="enableVariants">
                                Bật biến thể
                            </label>
                        </div>
                    </div>
                    <div class="card-body" id="variantsSection" style="{{ old('enable_variants', $product->variants->count() > 0) ? '' : 'display: none;' }}">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Biến thể giúp bạn tạo các phiên bản khác nhau của sản phẩm (màu sắc, kích thước, v.v.)
                        </div>
                        
                        <div id="variants-wrapper">
                            @foreach(old('variants', $product->variants->toArray()) as $index => $variant)
                                <div class="variant-item border rounded p-3 mb-3 bg-light">
                                    @if(!empty($variant['variant_id']))
                                        <input type="hidden" name="variants[{{ $index }}][variant_id]" value="{{ $variant['variant_id'] }}">
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-success">
                                            <i class="fas fa-tag me-1"></i>Biến thể #{{ $index + 1 }}
                                        </h6>
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-variant">
                                            <i class="fas fa-trash me-1"></i>Xóa
                                        </button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Tên biến thể</label>
                                                <input type="text" 
                                                       name="variants[{{ $index }}][variant_name]" 
                                                       class="form-control" 
                                                       placeholder="VD: Màu đỏ, Size L..." 
                                                       value="{{ $variant['variant_name'] }}"
                                                       required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Giá thêm</label>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           name="variants[{{ $index }}][additional_price]" 
                                                           class="form-control" 
                                                           step="0.01" 
                                                           value="{{ $variant['additional_price'] ?? 0 }}"
                                                           placeholder="0.00">
                                                    <span class="input-group-text">₫</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Tồn kho</label>
                                                <input type="number" 
                                                       name="variants[{{ $index }}][stock_quantity]" 
                                                       class="form-control" 
                                                       min="0" 
                                                       value="{{ $variant['stock_quantity'] ?? 0 }}"
                                                       placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" class="btn btn-outline-success" id="add-variant">
                            <i class="fas fa-plus me-1"></i>Thêm biến thể
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Product Status -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-toggle-on me-2"></i>Trạng thái
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                                   {{ old('status', $product->status ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="status">
                                Kích hoạt sản phẩm
                            </label>
                        </div>
                        <div class="form-text">Sản phẩm sẽ hiển thị trên website khi được kích hoạt</div>
                    </div>
                </div>

                <!-- Product Image -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-image me-2"></i>Hình ảnh sản phẩm
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <div id="imagePreview" class="border rounded p-3 bg-light">
                                @if($product->image_url)
                                    <img src="{{ asset('storage/' . $product->image_url) }}" 
                                         alt="Current Image" 
                                         class="img-fluid rounded shadow-sm"
                                         style="max-height: 200px;">
                                    <div class="mt-2">
                                        <small class="text-muted">Ảnh hiện tại</small>
                                    </div>
                                @else
                                    <div class="text-muted py-5">
                                        <i class="fas fa-image fa-3x mb-3"></i>
                                        <p>Chưa có ảnh</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <input type="file" 
                               class="form-control" 
                               id="image" 
                               name="image" 
                               accept="image/*">
                        <div class="form-text">Chọn ảnh mới để thay thế (JPG, PNG, GIF - Tối đa 2MB)</div>
                    </div>
                </div>

                <!-- Product Summary -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>Tóm tắt
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="summary-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Tên sản phẩm:</span>
                            <span id="summaryName" class="fw-bold">{{ $product->name }}</span>
                        </div>
                        <div class="summary-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Giá bán:</span>
                            <span id="summaryPrice" class="fw-bold text-success">{{ number_format($product->price) }}₫</span>
                        </div>
                        <div class="summary-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Tồn kho:</span>
                            <span id="summaryStock" class="fw-bold">{{ $product->stock_quantity }}</span>
                        </div>
                        <div class="summary-item d-flex justify-content-between mb-2">
                            <span class="text-muted">Danh mục:</span>
                            <span id="summaryCategory" class="fw-bold">{{ $product->category->category_name ?? 'Chưa chọn' }}</span>
                        </div>
                        <div class="summary-item d-flex justify-content-between">
                            <span class="text-muted">Biến thể:</span>
                            <span id="summaryVariants" class="fw-bold">{{ $product->variants->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-2">
                            <i class="fas fa-save me-2"></i>Cập nhật sản phẩm
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-2"></i>Hủy bỏ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const enableVariantsCheckbox = document.getElementById('enableVariants');
    const variantsSection = document.getElementById('variantsSection');
    const variantsWrapper = document.getElementById('variants-wrapper');
    const addVariantBtn = document.getElementById('add-variant');
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');

    // Form inputs for summary
    const nameInput = document.getElementById('name');
    const priceInput = document.getElementById('price');
    const stockInput = document.getElementById('stock_quantity');
    const categorySelect = document.getElementById('category_id');

    // Summary elements
    const summaryName = document.getElementById('summaryName');
    const summaryPrice = document.getElementById('summaryPrice');
    const summaryStock = document.getElementById('summaryStock');
    const summaryCategory = document.getElementById('summaryCategory');
    const summaryVariants = document.getElementById('summaryVariants');

    let variantIndex = {{ count(old('variants', $product->variants->toArray())) }};

    // Toggle variants section
    enableVariantsCheckbox.addEventListener('change', function() {
        if (this.checked) {
            variantsSection.style.display = 'block';
            variantsSection.classList.add('fade-in');
        } else {
            variantsSection.style.display = 'none';
            variantsSection.classList.remove('fade-in');
        }
        updateVariantsSummary();
    });

    // Add variant
    addVariantBtn.addEventListener('click', function() {
        const variantHtml = `
            <div class="variant-item border rounded p-3 mb-3 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-success">
                        <i class="fas fa-tag me-1"></i>Biến thể #${variantIndex + 1}
                    </h6>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-variant">
                        <i class="fas fa-trash me-1"></i>Xóa
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên biến thể</label>
                            <input type="text" 
                                   name="variants[${variantIndex}][variant_name]" 
                                   class="form-control" 
                                   placeholder="VD: Màu đỏ, Size L..." 
                                   required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Giá thêm</label>
                            <div class="input-group">
                                <input type="number" 
                                       name="variants[${variantIndex}][additional_price]" 
                                       class="form-control" 
                                       step="0.01" 
                                       value="0"
                                       placeholder="0.00">
                                <span class="input-group-text">₫</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tồn kho</label>
                            <input type="number" 
                                   name="variants[${variantIndex}][stock_quantity]" 
                                   class="form-control" 
                                   min="0" 
                                   value="0"
                                   placeholder="0">
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        variantsWrapper.insertAdjacentHTML('beforeend', variantHtml);
        variantIndex++;
        updateVariantsSummary();
        
        // Add event listener to new remove button
        const newVariant = variantsWrapper.lastElementChild;
        const removeBtn = newVariant.querySelector('.remove-variant');
        removeBtn.addEventListener('click', function() {
            newVariant.remove();
            updateVariantsSummary();
            refreshVariantIndexes();
        });
    });

    // Remove variant (existing variants)
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variant') || e.target.closest('.remove-variant')) {
            const variantItem = e.target.closest('.variant-item');
            if (variantItem) {
                variantItem.remove();
                updateVariantsSummary();
                refreshVariantIndexes();
            }
        }
    });

    // Image preview
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = `
                    <img src="${e.target.result}" 
                         alt="Preview" 
                         class="img-fluid rounded shadow-sm"
                         style="max-height: 200px;">
                    <div class="mt-2">
                        <small class="text-success">Ảnh mới được chọn</small>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }
    });

    // Update summary
    function updateSummary() {
        if (nameInput.value) {
            summaryName.textContent = nameInput.value;
        }
        
        if (priceInput.value) {
            summaryPrice.textContent = new Intl.NumberFormat('vi-VN').format(priceInput.value) + '₫';
        }
        
        if (stockInput.value) {
            summaryStock.textContent = stockInput.value;
        }
        
        if (categorySelect.value) {
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            summaryCategory.textContent = selectedOption.text;
        }
    }

    function updateVariantsSummary() {
        const variantCount = enableVariantsCheckbox.checked ? 
            variantsWrapper.querySelectorAll('.variant-item').length : 0;
        summaryVariants.textContent = variantCount;
    }

    function refreshVariantIndexes() {
        const variants = variantsWrapper.querySelectorAll('.variant-item');
        variants.forEach((variant, index) => {
            // Update hidden input if exists
            const hiddenInput = variant.querySelector('input[type="hidden"][name$="[variant_id]"]');
            if (hiddenInput) {
                hiddenInput.name = `variants[${index}][variant_id]`;
            }

            // Update other inputs
            const variantNameInput = variant.querySelector('input[name$="[variant_name]"]');
            if (variantNameInput) variantNameInput.name = `variants[${index}][variant_name]`;

            const additionalPriceInput = variant.querySelector('input[name$="[additional_price]"]');
            if (additionalPriceInput) additionalPriceInput.name = `variants[${index}][additional_price]`;

            const stockQuantityInput = variant.querySelector('input[name$="[stock_quantity]"]');
            if (stockQuantityInput) stockQuantityInput.name = `variants[${index}][stock_quantity]`;

            // Update header
            const header = variant.querySelector('h6');
            if (header) {
                header.innerHTML = `<i class="fas fa-tag me-1"></i>Biến thể #${index + 1}`;
            }
        });
        
        variantIndex = variants.length;
    }

    // Event listeners for summary updates
    nameInput.addEventListener('input', updateSummary);
    priceInput.addEventListener('input', updateSummary);
    stockInput.addEventListener('input', updateSummary);
    categorySelect.addEventListener('change', updateSummary);

    // Initialize
    updateVariantsSummary();
});
</script>

<style>
.fade-in {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.variant-item {
    transition: all 0.3s ease;
}

.variant-item:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.btn {
    transition: all 0.3s ease;
}

.summary-item {
    padding: 0.25rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.summary-item:last-child {
    border-bottom: none;
}
</style>
@endpush
@endsection
