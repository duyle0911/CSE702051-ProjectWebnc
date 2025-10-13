@extends('layouts.admin')

@section('title', 'Thêm sản phẩm mới')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus-circle me-2 text-primary"></i>Thêm sản phẩm mới
            </h1>
            <nav aria-label="breadcrumb" class="mt-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                    <li class="breadcrumb-item active">Thêm mới</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Quay lại
            </a>
        </div>
    </div>

    <!-- Error Messages -->
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

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf
        
        <div class="row">
            <!-- Left Column - Main Info -->
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
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label fw-bold">
                                    Tên sản phẩm <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" 
                                       placeholder="Nhập tên sản phẩm..."
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Tên sản phẩm sẽ hiển thị cho khách hàng</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label fw-bold">
                                    Danh mục <span class="text-danger">*</span>
                                </label>
                                <select id="category_id" 
                                        name="category_id" 
                                        class="form-select @error('category_id') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}" 
                                                {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-bold">Trạng thái</label>
                                <select id="status" name="status" class="form-select">
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                        Hoạt động
                                    </option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                        Tạm ngưng
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label fw-bold">
                                    Mô tả sản phẩm <span class="text-danger">*</span>
                                </label>
                                <textarea id="description" 
                                          name="description" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          rows="5" 
                                          placeholder="Nhập mô tả chi tiết về sản phẩm..."
                                          required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Mô tả chi tiết giúp khách hàng hiểu rõ hơn về sản phẩm</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Inventory -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-dollar-sign me-2"></i>Giá & Tồn kho
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label fw-bold">
                                    Giá bán <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           id="price" 
                                           name="price" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           value="{{ old('price') }}" 
                                           step="0.01" 
                                           min="0"
                                           placeholder="0.00"
                                           required>
                                    <span class="input-group-text">₫</span>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="stock_quantity" class="form-label fw-bold">
                                    Số lượng tồn kho <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       id="stock_quantity" 
                                       name="stock_quantity" 
                                       class="form-control @error('stock_quantity') is-invalid @enderror" 
                                       value="{{ old('stock_quantity', 0) }}" 
                                       min="0"
                                       required>
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Variants -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-layer-group me-2"></i>Biến thể sản phẩm
                        </h5>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="enableVariants">
                            <label class="form-check-label fw-bold" for="enableVariants">
                                Bật biến thể
                            </label>
                        </div>
                    </div>
                    <div class="card-body" id="variantsSection" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Lưu ý:</strong> Biến thể giúp bạn tạo các phiên bản khác nhau của sản phẩm (màu sắc, kích thước, v.v.)
                        </div>
                        
                        <div id="variants-wrapper">
                            <!-- Variants will be added here dynamically -->
                        </div>

                        <button type="button" class="btn btn-outline-primary" id="add-variant">
                            <i class="fas fa-plus me-1"></i>Thêm biến thể
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column - Image & Actions -->
            <div class="col-lg-4">
                <!-- Product Image -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-image me-2"></i>Hình ảnh sản phẩm
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="image" class="form-label fw-bold">Hình ảnh chính</label>
                            <input type="file" 
                                   id="image" 
                                   name="image" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Định dạng: JPG, PNG, GIF. Tối đa 2MB</div>
                        </div>

                        <!-- Image Preview -->
                        <div id="imagePreview" class="text-center" style="display: none;">
                            <img id="previewImg" src="/placeholder.svg" alt="Preview" class="img-fluid rounded border" style="max-height: 200px;">
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-danger" id="removeImage">
                                    <i class="fas fa-trash me-1"></i>Xóa ảnh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bolt me-2"></i>Thao tác
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Lưu sản phẩm
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Hủy bỏ
                            </a>
                        </div>
                        
                        <!-- Product Info Summary -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="fw-bold mb-2">
                                <i class="fas fa-info-circle me-1"></i>Thông tin tóm tắt
                            </h6>
                            <small class="text-muted">
                                <div class="mb-1">
                                    <strong>Tên:</strong> <span id="summaryName">Chưa nhập</span>
                                </div>
                                <div class="mb-1">
                                    <strong>Giá:</strong> <span id="summaryPrice">0₫</span>
                                </div>
                                <div class="mb-1">
                                    <strong>Tồn kho:</strong> <span id="summaryStock">0</span>
                                </div>
                                <div>
                                    <strong>Danh mục:</strong> <span id="summaryCategory">Chưa chọn</span>
                                </div>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let variantIndex = 0;

    // Enable/Disable Variants
    const enableVariants = document.getElementById('enableVariants');
    const variantsSection = document.getElementById('variantsSection');
    
    enableVariants.addEventListener('change', function() {
        if (this.checked) {
            variantsSection.style.display = 'block';
            addVariant(); // Add first variant automatically
        } else {
            variantsSection.style.display = 'none';
            document.getElementById('variants-wrapper').innerHTML = '';
            variantIndex = 0;
        }
    });

    // Add Variant Function
    function addVariant() {
        const wrapper = document.getElementById('variants-wrapper');
        const variantHtml = `
            <div class="variant-item border rounded p-3 mb-3 bg-light" data-variant="${variantIndex}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-primary">
                        <i class="fas fa-tag me-1"></i>Biến thể #${variantIndex + 1}
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tên biến thể <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="variants[${variantIndex}][variant_name]" 
                               class="form-control" 
                               placeholder="VD: Màu đỏ, Size L" 
                               required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Giá thêm</label>
                        <div class="input-group">
                            <input type="number" 
                                   name="variants[${variantIndex}][additional_price]" 
                                   class="form-control" 
                                   step="0.01" 
                                   value="0"
                                   min="0">
                            <span class="input-group-text">₫</span>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Tồn kho</label>
                        <input type="number" 
                               name="variants[${variantIndex}][stock_quantity]" 
                               class="form-control" 
                               value="0" 
                               min="0"
                               required>
                    </div>
                </div>
            </div>
        `;
        
        wrapper.insertAdjacentHTML('beforeend', variantHtml);
        variantIndex++;
        
        // Add event listener for remove button
        const removeButtons = wrapper.querySelectorAll('.remove-variant');
        removeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.variant-item').remove();
            });
        });
    }

    // Add Variant Button
    document.getElementById('add-variant').addEventListener('click', addVariant);

    // Image Preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImageBtn = document.getElementById('removeImage');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    removeImageBtn.addEventListener('click', function() {
        imageInput.value = '';
        imagePreview.style.display = 'none';
        previewImg.src = '';
    });

    // Real-time Summary Update
    const nameInput = document.getElementById('name');
    const priceInput = document.getElementById('price');
    const stockInput = document.getElementById('stock_quantity');
    const categorySelect = document.getElementById('category_id');

    const summaryName = document.getElementById('summaryName');
    const summaryPrice = document.getElementById('summaryPrice');
    const summaryStock = document.getElementById('summaryStock');
    const summaryCategory = document.getElementById('summaryCategory');

    nameInput.addEventListener('input', function() {
        summaryName.textContent = this.value || 'Chưa nhập';
    });

    priceInput.addEventListener('input', function() {
        const price = parseFloat(this.value) || 0;
        summaryPrice.textContent = new Intl.NumberFormat('vi-VN').format(price) + '₫';
    });

    stockInput.addEventListener('input', function() {
        summaryStock.textContent = this.value || '0';
    });

    categorySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        summaryCategory.textContent = selectedOption.text === '-- Chọn danh mục --' ? 'Chưa chọn' : selectedOption.text;
    });

    // Form Validation
    const form = document.getElementById('productForm');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
            // Scroll to first error
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }
    });

    // Auto-save to localStorage (Draft feature)
    const formInputs = form.querySelectorAll('input, textarea, select');
    formInputs.forEach(input => {
        // Load saved data
        const savedValue = localStorage.getItem('product_draft_' + input.name);
        if (savedValue && !input.value) {
            input.value = savedValue;
            // Trigger change event to update summary
            input.dispatchEvent(new Event('input'));
            input.dispatchEvent(new Event('change'));
        }

        // Save data on change
        input.addEventListener('input', function() {
            localStorage.setItem('product_draft_' + this.name, this.value);
        });
    });

    // Clear draft on successful submit
    form.addEventListener('submit', function() {
        if (this.checkValidity()) {
            formInputs.forEach(input => {
                localStorage.removeItem('product_draft_' + input.name);
            });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.card {
    border: none;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
    font-weight: 600;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #e3e6f0;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    transform: translateY(-1px);
}

.btn {
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.variant-item {
    transition: all 0.3s ease;
    border: 2px dashed #dee2e6 !important;
}

.variant-item:hover {
    border-color: #4e73df !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.breadcrumb {
    background: none;
    padding: 0;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
    color: #6c757d;
}

#imagePreview img {
    transition: all 0.3s ease;
    border: 3px solid #e3e6f0;
}

#imagePreview img:hover {
    transform: scale(1.05);
    border-color: #4e73df;
}

.form-check-input:checked {
    background-color: #4e73df;
    border-color: #4e73df;
}

.alert {
    border-radius: 8px;
    border: none;
}

.bg-light {
    background-color: #f8f9fc !important;
}

.text-primary {
    color: #4e73df !important;
}

/* Animation for variant items */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.variant-item {
    animation: slideIn 0.3s ease;
}

/* Loading state for submit button */
.btn-primary:disabled {
    background-color: #6c757d;
    border-color: #6c757d;
}
</style>
@endpush
@endsection
