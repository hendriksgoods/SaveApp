@extends('layouts.app')
@section('title', 'Lupa Password – SaveThem')

@section('content')
<div class="bg-light py-5" style="min-height:80vh;">
<div class="container">
<div class="row justify-content-center">
<div class="col-md-5">

    <div class="card border-0 shadow-sm p-4 p-md-5">

        <div class="text-center mb-4">
            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                 style="width:64px;height:64px;border-radius:50%;background:#EBF5FF;">
                <i class="bi bi-lock text-primary" style="font-size:28px;"></i>
            </div>
            <h5 class="fw-800 mb-1">Lupa Password?</h5>
            <p class="text-muted small mb-0">
                Masukkan email Anda dan kami akan memberikan link untuk reset password.
            </p>
        </div>

        {{-- Success + Reset URL (karena tidak ada email server) --}}
        @if(session('success'))
        <div class="alert alert-success small mb-3">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>
            {{ session('success') }}
        </div>
        @if(session('reset_url'))
        <div class="alert alert-info small mb-3">
            <p class="fw-700 mb-2">
                <i class="bi bi-info-circle me-1"></i>
                Link Reset Password (klik untuk reset):
            </p>
            <a href="{{ session('reset_url') }}" class="btn btn-primary w-100 fw-700">
                <i class="bi bi-key me-1"></i>Klik di sini untuk Reset Password
            </a>
            <p class="text-muted mt-2 mb-0" style="font-size:11px;">
                ⚠️ Link ini berlaku selama 1 jam.
            </p>
        </div>
        @endif
        @endif

        @if(!session('reset_url'))
        <form action="{{ route('password.send') }}" method="POST">
        @csrf
            <div class="mb-3">
                <label class="form-label fw-600 small">Email <span class="text-danger">*</span></label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="email@kamu.com" autofocus required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-700 py-2">
                <i class="bi bi-send me-1"></i>Kirim Link Reset
            </button>
        </form>
        @endif

        <hr class="my-3">
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-primary small fw-600">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Login
            </a>
        </div>

    </div>
</div>
</div>
</div>
@endsection
