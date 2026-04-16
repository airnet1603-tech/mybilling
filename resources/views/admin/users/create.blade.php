@extends('layouts.admin')
@section('title', '{{ isset($user) ? "Edit" : "Tambah" }} User - ISP Billing')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-user-plus me-2 text-danger"></i>
            {{ isset($user) ? 'Edit User: '.$user->name : 'Tambah User Baru' }}
        </h5>
        <a href="/admin/users" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $e)
            <div><i class="fas fa-exclamation-circle me-1"></i>{{ $e }}</div>
        @endforeach
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ isset($user) ? '/admin/users/'.$user->id : '/admin/users' }}">
                @csrf
                @if(isset($user)) @method('PUT') @endif
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Password {{ isset($user) ? '(kosongkan jika tidak diubah)' : '*' }}</label>
                        <input type="password" name="password" class="form-control" {{ isset($user)?'':'required' }} minlength="6">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="role-card {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}" onclick="selectRole(this,'admin')">
                                    <input type="radio" name="role" value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'checked' : '' }} style="display:none">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width:40px;height:40px;background:rgba(233,69,96,0.1);border-radius:8px 0px 8px 0px;display:flex;align-items:center;justify-content:center">
                                            <i class="fas fa-crown text-danger"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Admin</div>
                                            <small class="text-muted">Akses penuh semua fitur</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <label class="role-card {{ old('role', $user->role ?? 'operator') === 'operator' ? 'selected' : '' }}" onclick="selectRole(this,'operator')">
                                    <input type="radio" name="role" value="operator" {{ old('role', $user->role ?? 'operator') === 'operator' ? 'checked' : '' }} style="display:none">
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="width:40px;height:40px;background:rgba(15,52,96,0.1);border-radius:8px 0px 8px 0px;display:flex;align-items:center;justify-content:center">
                                            <i class="fas fa-user-tie" style="color:#0f3460"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">Operator</div>
                                            <small class="text-muted">Pelanggan, tagihan, bayar</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-save px-4">
                            <i class="fas fa-save me-2"></i>{{ isset($user) ? 'Simpan Perubahan' : 'Tambah User' }}
                        </button>
                        <a href="/admin/users" class="btn btn-outline-secondary ms-2">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('styles')
<style>
    .form-control:focus, .form-select:focus { border-color: #e94560; box-shadow: 0 0 0 0.2rem rgba(233,69,96,0.15); }
    .btn-save { background: linear-gradient(135deg, #e94560, #0f3460); border: none; color: #fff; }
    .btn-save:hover { opacity: 0.9; color: #fff; }
    .role-card { border: 2px solid #dee2e6; border-radius: 10px; padding: 15px; cursor: pointer; transition: .2s; display: block; }
    .role-card:hover { border-color: #e94560; }
    .role-card.selected { border-color: #e94560; background: rgba(233,69,96,0.05); }
</style>
@endpush

@push('scripts')
<script>
function selectRole(el, val) {
    document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input').checked = true;
}
</script>
@endpush
