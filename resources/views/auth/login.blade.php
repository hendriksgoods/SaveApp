@extends('layouts.app')
@section('title','Masuk – SaveThem')
@section('content')
<div class="min-vh-80 d-flex align-items-center justify-content-center bg-light py-5">
<div class="auth-card card border-0 shadow p-4 p-md-5">

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h4 class="fw-800 mb-1">Masuk</h4>
            <p class="text-muted small mb-0">Selamat datang kembali</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-sm btn-light border"><i class="bi bi-x-lg"></i></a>
    </div>

    <form action="{{ route('login') }}" method="POST">
    @csrf
    <div class="mb-3">
        <label class="form-label fw-600 small">Email</label>
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-envelope text-muted"></i></span>
            <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror"
                   name="email" value="{{ old('email') }}" placeholder="email@example.com" autofocus>
        </div>
        @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>   
    <div class="mb-2">
        <div class="d-flex justify-content-between mb-1">
            <label class="form-label fw-600 small mb-0">Password</label>
        </div>
        <div class="input-group">
            <span class="input-group-text bg-white"><i class="bi bi-lock text-muted"></i></span>
            <input type="password" class="form-control border-start-0" id="loginPwd"
                   name="password" placeholder="Password" required>
            <button type="button" class="btn btn-outline-secondary" id="togglePwd">
                <i class="bi bi-eye" id="eyeIcon"></i>
            </button>
        </div>
    </div>

    
    {{-- Lupa Password --}}
    <div class="text-end mb-4">
        <a href="{{ route('password.forgot') }}" class="small text-primary fw-600">
            Lupa password?
        </a>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 fw-700">Masuk</button>
    </form>
        
    <hr class="my-4">
    <p class="text-center text-muted small mb-0">
        Belum punya akun? <a href="{{ route('register') }}" class="text-primary fw-700">Daftar</a>
    </p>
</div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('togglePwd').addEventListener('click', function(){
    const p = document.getElementById('loginPwd');
    const i = document.getElementById('eyeIcon');
    p.type = p.type === 'password' ? 'text' : 'password';
    i.className = p.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
});
</script>
@endpush
