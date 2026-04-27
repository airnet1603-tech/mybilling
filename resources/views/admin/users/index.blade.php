@extends('layouts.admin')
@section('title', 'Kelola User - ISP Billing')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 fw-bold"><i class="fas fa-user-cog me-2 text-danger"></i>Kelola User Billing</h5>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <span class="fw-semibold">Daftar User Billing</span>
            <a href="/admin/users/create" class="btn btn-sm btn-danger">
                <i class="fas fa-plus me-1"></i> Tambah User
            </a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                    <tr>
                        <td class="ps-4">{{ $i+1 }}</td>
                        <td>
                            <div class="fw-semibold">{{ $user->name }}</div>
                            @if($user->id === auth()->id())
                                <small class="text-muted">(Anda)</small>
                            @endif
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role === 'superadmin')
                                <span class="badge-admin" style="background:#6f42c1;color:#fff;padding:3px 8px;border-radius:4px;"><i class="fas fa-shield-alt me-1"></i>Super Admin</span>
                            @elseif($user->role === 'admin')
                                <span class="badge-admin"><i class="fas fa-crown me-1"></i>Admin</span>
                            @else
                                <span class="badge-operator"><i class="fas fa-user me-1"></i>Operator</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <a href="/admin/users/{{ $user->id }}/edit" class="btn btn-sm btn-outline-primary me-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="/admin/users/{{ $user->id }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada user</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
