@extends('layouts.admin')

@push('styles')
<style>
    .table td, .table th { padding-top: 0px !important; padding-bottom: 0px !important; vertical-align: middle !important; }
    .stat-card { border:none; border-radius:12px; padding:15px 20px; color:white; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="fw-bold mb-0">Riwayat Pembayaran</h5>
        <small class="text-muted">{{ now()->isoFormat('MMMM Y') }}</small>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#4facfe,#00f2fe)">
            <div class="fs-5 fw-bold" id="statBulanIni">Rp {{ number_format($totalBulanIni,0,',','.') }}</div>
            <div class="opacity-75">Total Bulan Ini</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#667eea,#764ba2)">
            <div class="fs-5 fw-bold" id="statTransaksi">{{ $totalTransaksi }}</div>
            <div class="opacity-75">Transaksi Bulan Ini</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#11998e,#38ef7d)">
            <div class="fs-5 fw-bold" id="statCash">Rp {{ number_format($totalCash,0,',','.') }}</div>
            <div class="opacity-75">Total Cash</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#f093fb,#f5576c)">
            <div class="fs-5 fw-bold" id="statTransfer">Rp {{ number_format($totalTransfer,0,',','.') }}</div>
            <div class="opacity-75">Total Transfer</div>
        </div>
    </div>
</div>

{{-- FILTER --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form id="filterForm" method="GET" action="/admin/pembayaran" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" id="searchInput" name="search" class="form-control form-control-sm"
                       placeholder="Cari nama / no pembayaran..." value="{{ request('search') }}"
                       oninput="clearTimeout(window._st);window._st=setTimeout(doAjaxFilter,400)">
            </div>
            <div class="col-md-2">
                <select name="metode" class="form-select form-select-sm" onchange="doAjaxFilter()">
                    <option value="">Semua Metode</option>
                    <option value="cash"     {{ request('metode')=='cash'     ? 'selected':'' }}>Cash</option>
                    <option value="transfer" {{ request('metode')=='transfer' ? 'selected':'' }}>Transfer</option>
                    <option value="midtrans" {{ request('metode')=='midtrans' ? 'selected':'' }}>Midtrans</option>
                    <option value="xendit"   {{ request('metode')=='xendit'   ? 'selected':'' }}>Xendit</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="router_id" class="form-select form-select-sm" onchange="onRouterChange()">
                    <option value="">Semua Router</option>
                    @foreach($routers as $router)
                    <option value="{{ $router->id }}" {{ request('router_id') == $router->id ? 'selected' : '' }}>
                        {{ $router->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="paket_id" class="form-select form-select-sm" onchange="doAjaxFilter()">
                    <option value="">Semua Paket</option>
                    @foreach($pakets as $paket)
                    <option value="{{ $paket->id }}" {{ request('paket_id') == $paket->id ? 'selected' : '' }}>
                        {{ $paket->nama_paket }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="user_id" class="form-select form-select-sm" onchange="doAjaxFilter()">
                    <option value="">Semua User</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ ucfirst($user->role) }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-auto">
                <a href="/admin/pembayaran" class="btn btn-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- TABEL --}}
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="small">No. Pembayaran</th>
                        <th class="small">Pelanggan</th>
                        <th class="small">No. Tagihan</th>
                        <th class="small">Jumlah</th>
                        <th class="small">Metode</th>
                        <th class="small">Tanggal</th>
                        <th class="small">Catatan</th>
                    </tr>
                </thead>
                <tbody id="pembayaranBody">
                    @include('admin.pembayaran._table')
                </tbody>
            </table>
        </div>
    </div>
    @if($pembayarans->hasPages())
    <div class="card-footer bg-white">
        {{ $pembayarans->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<script>
var paketsByRouter = {!! $paketsByRouter !!};

function onRouterChange() {
    var routerId = document.querySelector('[name=router_id]').value;
    var paketSelect = document.querySelector('[name=paket_id]');
    paketSelect.innerHTML = '<option value="">Semua Paket</option>';
    if (routerId && paketsByRouter[routerId]) {
        paketsByRouter[routerId].forEach(function(paket) {
            var opt = document.createElement('option');
            opt.value = paket.id;
            opt.textContent = paket.nama_paket;
            paketSelect.appendChild(opt);
        });
    } else {
        var allPakets = [];
        Object.values(paketsByRouter).forEach(function(pakets) {
            pakets.forEach(function(p) {
                if (!allPakets.find(x => x.id === p.id)) allPakets.push(p);
            });
        });
        allPakets.sort((a,b) => a.nama_paket.localeCompare(b.nama_paket));
        allPakets.forEach(function(paket) {
            var opt = document.createElement('option');
            opt.value = paket.id;
            opt.textContent = paket.nama_paket;
            paketSelect.appendChild(opt);
        });
    }
    doAjaxFilter();
}

function doAjaxFilter() {
    var f = document.getElementById('filterForm');
    var params = new URLSearchParams();
    var search   = f.querySelector('[name=search]').value;
    var metode   = f.querySelector('[name=metode]').value;
    var router   = f.querySelector('[name=router_id]').value;
    var paket    = f.querySelector('[name=paket_id]').value;

    var user_id  = f.querySelector('[name=user_id]').value;
    if (search) params.set('search', search);
    if (metode) params.set('metode', metode);
    if (router) params.set('router_id', router);
    if (paket)  params.set('paket_id', paket);

    if (user_id) params.set('user_id', user_id);

    fetch('/admin/pembayaran?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('pembayaranBody').innerHTML = data.html;
        document.getElementById('statBulanIni').textContent  = 'Rp ' + data.totalBulanIni;
        document.getElementById('statTransaksi').textContent = data.totalTransaksi;
        document.getElementById('statCash').textContent      = 'Rp ' + data.totalCash;
        document.getElementById('statTransfer').textContent  = 'Rp ' + data.totalTransfer;
        history.pushState(null, '', '/admin/pembayaran?' + params.toString());
    });
}
</script>

@endsection
