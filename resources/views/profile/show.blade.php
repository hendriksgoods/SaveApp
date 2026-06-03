@extends('layouts.app')
@section('title','Profil Saya – SaveThem')

@section('content')
<div class="bg-light py-5" style="min-height:80vh;">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-6">

    <div class="card border-0 shadow-sm p-4 p-md-5">

        {{-- Avatar & Name --}}
        <div class="text-center mb-4">
            <div class="mx-auto mb-3 d-flex align-items-center justify-content-center fw-800 text-white"
                 style="width:72px;height:72px;border-radius:50%;font-size:28px;
                 background:{{ $user->isPenggalang() ? '#1B6CA8' : ($user->isAdmin() ? '#7C3AED' : '#F97316') }};">
                {{ strtoupper(substr($user->name,0,1)) }}
            </div>
            <h4 class="fw-800 mb-1">{{ $user->name }}</h4>
            <span class="badge px-3 py-2"
                  style="background:{{ $user->isPenggalang() ? '#EBF5FF' : ($user->isAdmin() ? '#F3F0FF' : '#FFF7ED') }};
                  color:{{ $user->isPenggalang() ? '#1B6CA8' : ($user->isAdmin() ? '#7C3AED' : '#F97316') }};">
                {{ match($user->role){ 'admin'=>'Admin','penggalang'=>'Penggalang Dana',default=>'Donatur' } }}
            </span>
        </div>

        {{-- Flash --}}
        @if(session('success'))
        <div class="alert alert-success small py-2 d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill text-success"></i>{{ session('success') }}
        </div>
        @endif

        {{-- Stats for penggalang --}}
        @if($user->isPenggalang())
        <div class="row g-2 mb-4">
            <div class="col-4">
                <div class="bg-light rounded-3 p-2 text-center">
                    <div class="fw-800">{{ $user->campaigns()->count() }}</div>
                    <div class="text-muted" style="font-size:11px;">Total Campaign</div>
                </div>
            </div>
            <div class="col-4">
                <div class="bg-light rounded-3 p-2 text-center">
                    <div class="fw-800 text-success">{{ $user->campaigns()->where('status','active')->count() }}</div>
                    <div class="text-muted" style="font-size:11px;">Aktif</div>
                </div>
            </div>
            <div class="col-4">
                <div class="bg-light rounded-3 p-2 text-center">
                    <div class="fw-800 text-primary">
                        Rp {{ number_format($user->campaigns()->sum('raised_amount')/1000000,1) }}Jt
                    </div>
                    <div class="text-muted" style="font-size:11px;">Terkumpul</div>
                </div>
            </div>
        </div>
        @endif

        {{-- Stats for donatur --}}
        @if($user->isDonatur())
        <div class="row g-2 mb-4">
            <div class="col-6">
                <div class="bg-light rounded-3 p-2 text-center">
                    <div class="fw-800">{{ $user->donations()->where('payment_status','paid')->count() }}</div>
                    <div class="text-muted" style="font-size:11px;">Total Donasi</div>
                </div>
            </div>
            <div class="col-6">
                <div class="bg-light rounded-3 p-2 text-center">
                    <div class="fw-800 text-primary">
                        Rp {{ number_format($user->donations()->where('payment_status','paid')->sum('amount')/1000000,1) }}Jt
                    </div>
                    <div class="text-muted" style="font-size:11px;">Total Didonasikan</div>
                </div>
            </div>
        </div>
        @endif

        <hr class="my-3">

        {{-- Edit Form --}}
        <h6 class="fw-700 mb-3">Edit Profil</h6>
        <form action="{{ route('profile.update') }}" method="POST" novalidate>
        @csrf @method('PUT')

            {{-- Nama (read only) --}}
            <div class="mb-3">
                <label class="form-label fw-600 small">Nama Lengkap</label>
                <input type="text" class="form-control bg-light" value="{{ $user->name }}"
                       readonly disabled>
                <div class="form-text">Nama tidak dapat diubah</div>
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label fw-600 small">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" id="profEmail"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email',$user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Nomor Telepon --}}
            <div class="mb-4">
                <label class="form-label fw-600 small">Nomor Telepon <span class="text-danger">*</span></label>
                <input type="text" name="phone" id="profPhone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone',$user->phone) }}"
                       placeholder="12 digit" maxlength="12" inputmode="numeric">
                <div class="form-text">Harus tepat 12 angka</div>
                <div class="text-danger small d-none mt-1" id="profPhoneErr">
                    Nomor telepon harus tepat 12 angka.
                </div>
                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-700">
                <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
            </button>

            <hr class="my-3">
        </form>
        
        <div class="text-center">
            @if($user->isPenggalang())
                <a href="{{ route('fundraiser.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                </a>
            @else
                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-house me-1"></i>Beranda
                </a>
            @endif
        </div>
    </div>

</div>
</div>
</div>
@endsection

@push('scripts')
<script>
// Phone: only digits, max 12
document.getElementById('profPhone').addEventListener('input', function(){
    this.value = this.value.replace(/[^0-9]/g,'');
    const err = document.getElementById('profPhoneErr');
    if(this.value.length > 0 && this.value.length !== 12){
        this.classList.add('is-invalid'); err.classList.remove('d-none');
    } else {
        this.classList.remove('is-invalid'); err.classList.add('d-none');
    }
});
</script>
@endpush
