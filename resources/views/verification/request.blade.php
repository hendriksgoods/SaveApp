@extends('layouts.app')
@section('title','Verifikasi Akun – SaveThem')

@section('content')
<div class="bg-light py-5" style="min-height:80vh;">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-6">



    {{-- Flash --}}
    @if(session('success'))
    <div class="alert alert-success small d-flex gap-2 align-items-center mb-4">
        <i class="bi bi-check-circle-fill text-success"></i>{{ session('success') }}
    </div>
    @endif


    @if(session('info'))
    <div class="alert alert-info small d-flex gap-2 align-items-center mb-4">
        <i class="bi bi-info-circle-fill text-info"></i>{{ session('info') }} 
    </div>
    @endif

    <div class="card border-0 shadow-sm p-4 p-md-5 text-center">

        @php $user = auth()->user(); @endphp

            <div class="card border-0 shadow-sm p-4 p-md-5">

        {{-- Header --}}
        <div class="text-center mb-4">
            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                 style="width:72px;height:72px;border-radius:50%;
                 background:{{ $user->verification_status==='approved' ? '#f0fdf4' : ($user->verification_status==='rejected' ? '#fef2f2' : ($user->verification_status==='pending' ? '#fffbeb' : '#FFF7ED')) }};">
                <i class="bi bi-{{ $user->verification_status==='approved' ? 'patch-check-fill text-success' : ($user->verification_status==='rejected' ? 'x-circle-fill text-danger' : ($user->verification_status==='pending' ? 'clock-fill text-warning' : 'shield-exclamation text-warning')) }}"
                   style="font-size:32px;"></i>
            </div>

            <h4 class="fw-800 mb-1">
                {{ match($user->verification_status) {
                    'pending'  => 'Pengajuan Sedang Direview',
                    'approved' => 'Akun Terverifikasi',
                    'rejected' => 'Pengajuan Ditolak',
                    default    => 'Verifikasi Akun Penggalang',
                } }}
            </h4>
            <p class="text-muted small mb-0">
                {{ match($user->verification_status) {
                    'pending'  => 'Pengajuan Anda sedang direview oleh admin (1-2 hari kerja)',
                    'approved' => 'Anda sudah bisa membuat campaign',
                    'rejected' => 'Pengajuan Anda ditolak. Perbaiki dan ajukan ulang.',
                    default    => 'Lengkapi data berikut untuk mengajukan verifikasi',
                } }}
            </p>
        </div>

        
               {{-- Rejected reason --}}
        @if($user->verification_status === 'rejected' && $user->rejection_reason)
        <div class="alert alert-danger d-flex gap-2 mb-4">
            <i class="bi bi-exclamation-circle-fill text-danger flex-shrink-0 mt-1"></i>
            <div>
                <strong class="small">Alasan Penolakan Admin:</strong>
                <div class="small mt-1">{{ $user->rejection_reason }}</div>
            </div>
        </div>
        @endif

         {{-- Approved state --}}
        @if($user->verification_status === 'approved')
        <div class="alert alert-success d-flex gap-2 mb-4">
            <i class="bi bi-check-circle-fill text-success flex-shrink-0"></i>
            <div class="small">Akun Anda sudah diverifikasi pada {{ $user->verified_at?->format('d M Y, H:i') }}. Anda bisa langsung membuat campaign.</div>
        </div>
        <a href="{{ route('campaigns.create') }}" class="btn btn-success w-100 fw-700 py-2 mb-3">
            <i class="bi bi-plus-lg me-1"></i>Buat Campaign Sekarang
        </a>

        {{-- Pending state --}}
        @elseif($user->verification_status === 'pending')
        <div class="card bg-light border-0 p-3 mb-4">
            <p class="fw-700 small mb-2">Data yang sudah dikirim:</p>
            <div class="small text-muted mb-1">
                <i class="bi bi-person me-1"></i><strong>Nama KTP:</strong> {{ $user->verify_full_name }}
            </div>
            <div class="small text-muted">
                <i class="bi bi-credit-card me-1"></i><strong>No. KTP:</strong> {{ $user->verify_ktp_number }}
            </div>
        </div>

        <p class="text-muted small mb-4">
                <div class="text-center text-muted small mb-3">
            <i class="bi bi-clock me-1"></i>Menunggu review admin...
    </div>
        {{-- None / Rejected = show form --}}
        @else
        <form action="{{ route('verification.submit') }}" method="POST" novalidate>
        @csrf

            <div class="mb-3">
                <label class="form-label fw-600 small">
                    Nama Lengkap Sesuai KTP <span class="text-danger">*</span>
                </label>
                <input type="text" name="verify_full_name" id="verifyName"
                       class="form-control @error('verify_full_name') is-invalid @enderror"
                       value="{{ old('verify_full_name', $user->verify_full_name) }}"
                       placeholder="Nama sesuai KTP (hanya huruf)">
                <div class="text-danger small d-none mt-1" id="verifyNameErr">
                    Nama hanya boleh berisi huruf dan spasi.
                </div>
                @error('verify_full_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-600 small">
                    Nomor KTP <span class="text-danger">*</span>
                </label>
                <input type="text" name="verify_ktp_number" id="verifyKtp"
                       class="form-control @error('verify_ktp_number') is-invalid @enderror"
                       value="{{ old('verify_ktp_number', $user->verify_ktp_number) }}"
                       placeholder="16 digit nomor KTP" maxlength="16" inputmode="numeric">
                <div class="text-danger small d-none mt-1" id="verifyKtpErr">
                    Nomor KTP harus tepat 16 angka.
                </div>
                @error('verify_ktp_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="alert alert-light border small mb-4">
                <i class="bi bi-shield-lock me-1 text-primary"></i>
                Data ini hanya digunakan untuk verifikasi identitas dan tidak akan dibagikan ke pihak lain.
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-700 py-2">
                <i class="bi bi-send me-1"></i>Kirim Pengajuan Verifikasi
            </button>
        </form>
        @endif

        <hr class="my-3">
        <a href="{{ route('fundraiser.dashboard') }}" class="btn btn-outline-secondary w-100 btn-sm">
            <i class="bi bi-speedometer2 me-1"></i>Kembali ke Dashboard
        </a>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
// Nama: hanya huruf dan spasi
document.getElementById('verifyName').addEventListener('input', function () {
    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
    const err = document.getElementById('verifyNameErr');
    if (this.value && /[0-9]/.test(this.value)) {
        this.classList.add('is-invalid'); err.classList.remove('d-none');
    } else {
        this.classList.remove('is-invalid'); err.classList.add('d-none');
    }
});

// KTP: hanya angka, tepat 16 digit
document.getElementById('verifyKtp').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
    const err = document.getElementById('verifyKtpErr');
    if (this.value.length > 0 && this.value.length !== 16) {
        this.classList.add('is-invalid'); err.classList.remove('d-none');
    } else {
        this.classList.remove('is-invalid'); err.classList.add('d-none');
    }
});
</script>
@endpush

