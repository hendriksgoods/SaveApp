@extends('layouts.app')
@section('title','Daftar Akun – SaveThem')
@section('content')
<div class="min-vh-80 d-flex align-items-center justify-content-center bg-light py-5">
<div class="auth-card card border-0 shadow p-4 p-md-5">

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h4 class="fw-800 mb-1">Daftar Akun</h4>
            <p class="text-muted small mb-0">Bergabung dengan SaveThem</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-sm btn-light border"><i class="bi bi-x-lg"></i></a>
    </div>

    <form action="{{ route('register') }}" method="POST" novalidate>
    @csrf

    {{-- Pilih Tipe Akun — hanya Donatur & Penggalang Dana --}}
    <div class="mb-4">
        <label class="form-label fw-600 small">Pilih Tipe Akun <span class="text-danger">*</span></label>
        <div class="row g-2">
            @foreach([
                ['donatur',    'bi-heart',   'Donatur',         'Untuk berdonasi'],
                ['penggalang', 'bi-people',  'Penggalang Dana', 'Buat campaign'],
            ] as [$val, $icon, $label, $sub])
            <div class="col-6">
                <input type="radio" class="btn-check" name="role" id="role_{{ $val }}"
                       value="{{ $val }}"
                       {{ old('role', 'donatur') === $val ? 'checked' : '' }}>
                <label class="btn btn-outline-secondary role-card w-100 py-3" for="role_{{ $val }}">
                    <i class="bi {{ $icon }} d-block fs-3 mb-1 text-muted"></i>
                    <span class="fw-700 small d-block">{{ $label }}</span>
                    <span class="text-muted d-block" style="font-size:11px;">{{ $sub }}</span>
                </label>
            </div>
            @endforeach
        </div>
        @error('role')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    {{-- Nama Lengkap --}}
    <div class="mb-3">
        <label class="form-label fw-600 small">Nama Lengkap</label>
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-person text-muted"></i></span>
            <input type="text"
                   class="form-control border-start-0 @error('name') is-invalid @enderror"
                   name="name" id="regName" value="{{ old('name') }}"
                   placeholder="Masukkan nama lengkap">
        </div>
        <div class="invalid-feedback d-block text-danger small" id="nameErr" style="display:none!important;"></div>
        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    {{-- Nomor Telepon --}}
    <div class="mb-3">
        <label class="form-label fw-600 small">Nomor Telepon</label>
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-telephone text-muted"></i></span>
            <input type="tel"
                   class="form-control border-start-0 @error('phone') is-invalid @enderror"
                   name="phone" id="regPhone" value="{{ old('phone') }}"
                   placeholder="08xxxxxxxxxx" maxlength="13">
        </div>
        <div class="text-danger small d-none" id="phoneErr">Nomor telepon harus 12 angka (contoh: 081234567890).</div>
        @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    {{-- Email --}}
    <div class="mb-3">
        <label class="form-label fw-600 small">Email</label>
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-envelope text-muted"></i></span>
            <input type="email"
                   class="form-control border-start-0 @error('email') is-invalid @enderror"
                   name="email" value="{{ old('email') }}"
                   placeholder="email@example.com">
        </div>
        @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    {{-- Password --}}
    <div class="mb-4">
        <label class="form-label fw-600 small">Password</label>
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-lock text-muted"></i></span>
            <input type="password"
                   class="form-control border-start-0 @error('password') is-invalid @enderror"
                   name="password" placeholder="Masukkan password">
            <button type="button" class="btn btn-outline-secondary" id="togglePwd">
                <i class="bi bi-eye" id="eyeReg"></i>
            </button>
        </div>
        @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-700" id="regSubmit">Daftar</button>
    </form>

    <p class="text-center text-muted small mt-3 mb-0">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-primary fw-700">Masuk</a>
    </p>
</div>
</div>
@endsection

@push('scripts')
<script>
// Toggle password
document.getElementById('togglePwd').addEventListener('click', function () {
    const p = document.querySelector('[name=password]');
    const i = document.getElementById('eyeReg');
    p.type = p.type === 'password' ? 'text' : 'password';
    i.className = p.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
});

// Name — hanya huruf & spasi, tidak boleh angka
document.getElementById('regName').addEventListener('input', function () {
    const err = document.getElementById('nameErr');
    if (this.value && /[0-9]/.test(this.value)) {
        this.classList.add('is-invalid');
        err.textContent = 'Nama tidak boleh mengandung angka.';
        err.style.display = 'block';
    } else {
        this.classList.remove('is-invalid');
        err.style.display = 'none';
    }
    // Otomatis hapus karakter angka
    this.value = this.value.replace(/[0-9]/g, '');
});

// Phone — hanya angka, tepat 12 digit
document.getElementById('regPhone').addEventListener('input', function () {
    const err  = document.getElementById('phoneErr');
    // Hapus karakter bukan angka
    this.value = this.value.replace(/[^0-9]/g, '');
    if (this.value.length > 0 && this.value.length !== 12) {
        this.classList.add('is-invalid');
        err.classList.remove('d-none');
    } else {
        this.classList.remove('is-invalid');
        err.classList.add('d-none');
    }
});
</script>
@endpush
