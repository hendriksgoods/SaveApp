@extends('layouts.app')
@section('title', 'Penarikan Dana – ' . $campaign->title)

@section('content')
<div class="bg-light py-4" style="min-height:80vh;">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-8">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-800 mb-0">Penarikan Dana</h4>
            <p class="text-muted small mb-0">{{ $campaign->title }}</p>
        </div>
        <a href="{{ route('campaigns.show', $campaign->slug) }}"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Campaign
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success small d-flex gap-2 align-items-center mb-3">
        <i class="bi bi-check-circle-fill text-success"></i>{{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger small d-flex gap-2 align-items-center mb-3">
        <i class="bi bi-x-circle-fill text-danger"></i>{{ session('error') }}
    </div>
    @endif

    {{-- Saldo Card --}}
    @php
        $totalWithdrawn = $withdrawals->where('status','processed')->sum('amount');
        $pending        = $withdrawals->where('status','pending')->sum('amount');
        $available      = $campaign->raised_amount - $totalWithdrawn - $pending;
    @endphp
    <div class="card border-0 shadow-sm mb-4 p-4"
         style="background:linear-gradient(135deg,#1B6CA8,#2563EB);">
        <div class="row g-3 text-white text-center">
            <div class="col-4">
                <div class="small text-white text-opacity-75">Dana Terkumpul</div>
                <div class="fw-800">Rp {{ number_format($campaign->raised_amount,0,',','.') }}</div>
            </div>
            <div class="col-4">
                <div class="small text-white text-opacity-75">Sudah Ditarik</div>
                <div class="fw-800">Rp {{ number_format($totalWithdrawn,0,',','.') }}</div>
            </div>
            <div class="col-4">
                <div class="small text-white text-opacity-75">Saldo Tersedia</div>
                <div class="fw-800 text-warning">Rp {{ number_format($available,0,',','.') }}</div>
            </div>
        </div>
    </div>

    {{-- Form Penarikan Baru --}}
    @if($available > 0 && !$withdrawals->where('status','pending')->count())
    <div class="card border-0 shadow-sm mb-4 p-4">
        <form action="{{ route('withdrawals.store', $campaign->slug) }}" method="POST">
        @csrf

            <div class="mb-3">
                <label class="form-label fw-600 small">Jumlah Penarikan <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text fw-600">Rp</span>
                    <input type="number" class="form-control @error('amount') is-invalid @enderror"
                           name="amount" id="withdrawAmount"
                           min="10000" max="{{ $available }}"
                           placeholder="0" required>
                </div>
                <div class="form-text" id="withdrawPreview">
                    Saldo tersedia: <strong>Rp {{ number_format($available,0,',','.') }}</strong>
                </div>
                @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-600 small">Bank <span class="text-danger">*</span></label>
                    <select class="form-select @error('bank_name') is-invalid @enderror" name="bank_name" required>
                        <option value="">Pilih bank...</option>
                        @foreach(['BCA','BNI','BRI','Mandiri','CIMB Niaga','Danamon','BSI','Jenius'] as $bank)
                        <option value="{{ $bank }}" {{ old('bank_name')===$bank?'selected':'' }}>{{ $bank }}</option>
                        @endforeach
                    </select>
                    @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-600 small">Nomor Rekening <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                           name="account_number" value="{{ old('account_number') }}"required>
                    @error('account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-600 small">Nama Pemilik <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('account_name') is-invalid @enderror"
                           name="account_name" value="{{ old('account_name') }}" required>
                    @error('account_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-600 small">
                Keterangan <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" name="description"
                       value="{{ old('description') }}"required>
                       @error('description')
                <div class="invalid-feedback">
                {{ $message }}
                </div>
                  @enderror
            </div>
            <button type="submit" class="btn btn-primary fw-700 px-4">
                <i class="bi bi-send me-1"></i>Tarik Dana
            </button>
        </form>
    </div>
    @elseif($withdrawals->where('status','pending')->count())
    <div class="alert alert-warning d-flex gap-2 mb-4">
        <i class="bi bi-clock-fill text-warning flex-shrink-0 mt-1"></i>
        <div class="small">
            <strong>Ada penarikan yang sedang diproses.</strong>
            Tunggu hingga selesai sebelum mengajukan penarikan baru.
        </div>
    </div>
    @elseif($available <= 0)
    <div class="alert alert-secondary small mb-4">
        Tidak ada saldo yang tersedia untuk ditarik.
    </div>
    @endif

    {{-- Riwayat Penarikan --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 px-4">
            <h6 class="fw-700 mb-0">Riwayat Penarikan Dana</h6>
        </div>

        @if($withdrawals->count())
        <div class="list-group list-group-flush">
            @foreach($withdrawals as $w)
            <div class="list-group-item px-4 py-3">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <div>
                        <span class="fw-700">Rp {{ number_format($w->amount,0,',','.') }}</span>
                        <span class="text-muted small ms-2">→ {{ $w->bank_name }} {{ $w->account_number }}</span>
                    </div>
                    <span class="badge px-2 py-1 fw-600"
                          style="font-size:11px;
                          @if($w->status==='processed') background:#f0fdf4;color:#16a34a;
                          @elseif($w->status==='pending') background:#fffbeb;color:#d97706;
                          @else background:#fef2f2;color:#dc2626; @endif">
                        {{ $w->status_label }}
                    </span>
                </div>

                <div class="text-muted small">
                    <i class="bi bi-person me-1"></i>{{ $w->account_name }}
                    @if($w->description)
                        · {{ $w->description }}
                    @endif
                </div>

                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted">
                        <i class="bi bi-calendar3 me-1"></i>
                        Diajukan {{ $w->created_at->format('d M Y, H:i') }}
                    </small>
                    @if($w->status === 'pending')
                    <small class="text-warning fw-600">
                        <i class="bi bi-clock me-1"></i>
                        Estimasi selesai: {{ $w->estimated }}
                    </small>
                    @elseif($w->status === 'processed')
                    <small class="text-success fw-600">
                        <i class="bi bi-check-circle me-1"></i>
                        Dicairkan {{ $w->processed_at?->format('d M Y, H:i') }}
                    </small>
                    @elseif($w->status === 'rejected')
                    <small class="text-danger fw-600">
                        <i class="bi bi-x-circle me-1"></i>
                        Ditolak: {{ $w->rejection_note }}
                    </small>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-wallet2 fs-1 d-block mb-2"></i>
            <p class="small mb-0">Belum ada riwayat penarikan</p>
        </div>
        @endif
    </div>

</div>
</div>
</div>
@endsection

@push('scripts')
<script>
const input = document.getElementById('withdrawAmount');
const preview = document.getElementById('withdrawPreview');
const available = {{ $available }};

if (input) {
    input.addEventListener('input', function () {
        const val = parseInt(this.value) || 0;
        const sisa = available - val;
        if (val > available) {
            this.classList.add('is-invalid');
            preview.innerHTML = '<span class="text-danger">Melebihi saldo tersedia!</span>';
        } else {
            this.classList.remove('is-invalid');
            preview.innerHTML = 'Sisa setelah tarik: <strong>Rp ' + sisa.toLocaleString('id-ID') + '</strong>';
        }
    });
}

document.addEventListener('input', function(e){

    if(e.target.name === 'account_number'){
        e.target.value = e.target.value.replace(/\D/g,'')
        .slice(0,16); //max 16 angka
    }

    if(e.target.name === 'account_name'){
        e.target.value = e.target.value.replace(/[^a-zA-Z\s]/g,'');
    }

});

</script>
@endpush
