@extends('layouts.app')
@section('title', 'Reset Password – SaveThem')

@section('content')
<div class="bg-light py-5" style="min-height:80vh;">
<div class="container">
<div class="row justify-content-center">
<div class="col-md-5">

    <div class="card border-0 shadow-sm p-4 p-md-5">

        <div class="text-center mb-4">
            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center"
                 style="width:64px;height:64px;border-radius:50%;background:#EBF5FF;">
                <i class="bi bi-key text-primary" style="font-size:28px;"></i>
            </div>
            <h5 class="fw-800 mb-1">Reset Password</h5>
            <p class="text-muted small mb-0">Masukkan password baru untuk akun <strong>{{ $email }}</strong></p>
        </div>

        <form action="{{ route('password.reset.post') }}" method="POST">
        @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-3">
                <label class="form-label fw-600 small">Password Baru <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" name="password" id="newPwd"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Minimal 8 karakter" required minlength="8">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('newPwd','eyeNew')">
                        <i class="bi bi-eye" id="eyeNew"></i>
                    </button>
                </div>
                {{-- Password strength --}}
                <div id="strengthBar" class="mt-2 d-none">
                    <div class="progress" style="height:4px;">
                        <div class="progress-bar" id="strengthProgress" style="width:0%;"></div>
                    </div>
                    <small id="strengthText" class="text-muted"></small>
                </div>
                @error('password')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-600 small">Konfirmasi Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="password" name="password_confirmation" id="confPwd"
                           class="form-control"
                           placeholder="Ulangi password baru" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePwd('confPwd','eyeConf')">
                        <i class="bi bi-eye" id="eyeConf"></i>
                    </button>
                </div>
                <div class="text-danger small d-none mt-1" id="matchErr">Password tidak cocok.</div>
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-700 py-2" id="resetBtn">
                <i class="bi bi-check-circle me-1"></i>Reset Password
            </button>
        </form>

    </div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

// Password strength
document.getElementById('newPwd').addEventListener('input', function () {
    const val  = this.value;
    const bar  = document.getElementById('strengthBar');
    const prog = document.getElementById('strengthProgress');
    const text = document.getElementById('strengthText');
    if (!val) { bar.classList.add('d-none'); return; }
    bar.classList.remove('d-none');
    let score = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        { w:'25%', cls:'bg-danger',  label:'Sangat lemah' },
        { w:'50%', cls:'bg-warning', label:'Lemah' },
        { w:'75%', cls:'bg-info',    label:'Cukup' },
        { w:'100%',cls:'bg-success', label:'Kuat' },
    ];
    const lvl = levels[score - 1] || levels[0];
    prog.style.width = lvl.w;
    prog.className   = 'progress-bar ' + lvl.cls;
    text.textContent = lvl.label;
});

// Check match
document.getElementById('confPwd').addEventListener('input', function () {
    const pwd  = document.getElementById('newPwd').value;
    const err  = document.getElementById('matchErr');
    const btn  = document.getElementById('resetBtn');
    if (this.value && this.value !== pwd) {
        err.classList.remove('d-none');
        this.classList.add('is-invalid');
        btn.disabled = true;
    } else {
        err.classList.add('d-none');
        this.classList.remove('is-invalid');
        btn.disabled = false;
    }
});
</script>
@endpush
