@extends('layouts.admin')

@section('title', 'Chỉnh sửa người dùng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mt-4">Chỉnh sửa người dùng</h1>
    <div>
        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info me-2">
            <i class="fas fa-eye me-2"></i>Xem chi tiết
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-edit me-1"></i>
                Thông tin người dùng
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+84...">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="role" class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required
                                    {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                <option value="">-- Chọn vai trò --</option>
                                <option value="customer" {{ old('role', $user->role) == 'customer' ? 'selected' : '' }}>Khách hàng</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                            </select>
                            @if($user->id === auth()->id())
                                <input type="hidden" name="role" value="{{ $user->role }}">
                                <small class="form-text text-muted">Bạn không thể thay đổi vai trò của chính mình</small>
                            @endif
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" name="address" rows="3" placeholder="Nhập địa chỉ đầy đủ...">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <h6 class="mb-3">Đổi mật khẩu (tùy chọn)</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">Mật khẩu mới</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="password-icon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Để trống nếu không muốn đổi mật khẩu</small>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                            <div class="input-group">
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye" id="password_confirmation-icon"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary me-2">Hủy</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Cập nhật
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- User Info Summary -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle me-1"></i>
                Thông tin hiện tại
            </div>
            <div class="card-body text-center">
                <div class="avatar-lg mx-auto mb-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-center" style="width: 60px; height: 60px; font-size: 1.5rem;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                </div>
                
                <h6>{{ $user->name }}</h6>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                
                @if($user->role === 'admin')
                    <span class="badge bg-danger mb-3">Quản trị viên</span>
                @else
                    <span class="badge bg-primary mb-3">Khách hàng</span>
                @endif

                <div class="text-start">
                    <small class="text-muted">
                        <strong>Tham gia:</strong> {{ $user->created_at->format('d/m/Y') }}<br>
                        <strong>Đơn hàng:</strong> {{ $user->orders->count() }} đơn<br>
                        <strong>Tổng chi tiêu:</strong> ${{ number_format($user->orders->where('status', 'delivered')->sum('total_amount'), 2) }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Guidelines -->
        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Lưu ý
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li><i class="fas fa-check text-success me-2"></i>Email phải là duy nhất</li>
                    <li><i class="fas fa-check text-success me-2"></i>Mật khẩu mới tối thiểu 8 ký tự</li>
                    @if($user->id === auth()->id())
                        <li><i class="fas fa-exclamation text-warning me-2"></i>Bạn không thể thay đổi vai trò của chính mình</li>
                    @endif
                    @if($user->orders->count() > 0)
                        <li><i class="fas fa-info text-info me-2"></i>Người dùng có {{ $user->orders->count() }} đơn hàng</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Validate password confirmation
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmation = this.value;
    
    if (password && password !== confirmation) {
        this.setCustomValidity('Mật khẩu xác nhận không khớp');
    } else {
        this.setCustomValidity('');
    }
});

// Clear password confirmation when password is cleared
document.getElementById('password').addEventListener('input', function() {
    const confirmation = document.getElementById('password_confirmation');
    if (!this.value) {
        confirmation.value = '';
        confirmation.setCustomValidity('');
    }
});
</script>
@endpush
@endsection