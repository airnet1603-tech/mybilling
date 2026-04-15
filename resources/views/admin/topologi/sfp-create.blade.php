@extends('layouts.admin')
@section('title', 'Tambah SFP')



@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="/admin/topologi" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left"></i></a>
    <h5 class="fw-bold mb-0"><i class="fas fa-plug me-2 text-primary"></i>Tambah SFP Baru</h5>
</div>

@if($errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card border-0 shadow-sm" style="border-radius:14px;">
            <div class="card-body p-4">
                <form method="POST" action="/admin/topologi/sfp/store">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">OLT Induk <span class="text-danger">*</span></label>
                        <select name="olt_id" class="form-select" required>
                            <option value="">-- Pilih OLT --</option>
                            @foreach($olts as $olt)
                            <option value="{{ $olt->id }}" {{ old('olt_id') == $olt->id ? 'selected' : '' }}>
                                {{ $olt->name }} ({{ $olt->ip_address }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama SFP <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: SFP1, GPON-0/1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Port <small class="text-muted">(opsional)</small></label>
                        <input type="text" name="port" class="form-control" value="{{ old('port') }}" placeholder="Contoh: 0/1, 1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan <small class="text-muted">(opsional)</small></label>
                        <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan') }}">

                        </div>
                    </div>                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-save me-1"></i> Simpan SFP
                        </button>
                        <a href="/admin/topologi" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
