<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Penggalang – Admin SaveThem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-light">

{{-- Header --}}
<div class="bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between sticky-top shadow-sm">
    <div>
        <h5 class="fw-800 mb-0">Verifikasi Akun Penggalang Dana</h5>
        <p class="text-muted small mb-0">Review dan verifikasi akun penggalang yang mengajukan</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Kembali
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-danger btn-sm">Logout</button>
        </form>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible m-0 rounded-0 border-0 border-start border-4 border-success py-2">
    <div class="container small"><i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
    <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button></div>
</div>
@endif

<div class="container py-4">

    {{-- ===== PENDING ===== --}}
    <div class="d-flex align-items-center gap-2 mb-3">
        <h5 class="fw-700 mb-0">Menunggu Review</h5>
        <span class="badge bg-warning text-dark px-2">{{ $pending->count() }}</span>
    </div>

    @if($pending->count())
    <div class="row g-3 mb-5">
        @foreach($pending as $user)
        <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="d-flex align-items-center justify-content-center fw-800 text-white flex-shrink-0"
                     style="width:44px;height:44px;border-radius:50%;background:#1B6CA8;font-size:18px;">
                    {{ strtoupper(substr($user->name,0,1)) }}
                </div>
                <div>
                    <div class="fw-700">{{ $user->name }}</div>
                    <div class="text-muted small">{{ $user->email }}</div>
                </div>
            </div>

            {{-- Detail --}}
            <div class="bg-light rounded-3 p-2 mb-3 small">
                <div class="text-muted mb-1">
                    <i class="bi bi-telephone me-1"></i>{{ $user->phone ?? '-' }}
                </div>
                <div class="text-muted mb-1">
                                        <i class="bi bi-person-badge me-1"></i>
                    <strong>Nama KTP:</strong> {{ $user->verify_full_name ?? '-' }}
                </div>
                <div class="text-muted mb-1">
                    <i class="bi bi-credit-card me-1"></i>
                    <strong>No. KTP:</strong> {{ $user->verify_ktp_number ?? '-' }}

                </div>
                <div class="text-muted">
                    <i class="bi bi-calendar3 me-1"></i>Daftar {{ $user->created_at->format('d M Y') }}
                </div>
            </div>

            {{-- Approve --}}
                        <form method="POST" action="{{ route('admin.verifications.approve', $user) }}" class="mb-2"
                  onsubmit="return confirm('Verifikasi akun {{ $user->name }}?')">

                @csrf
                <button type="submit" class="btn btn-success w-100 fw-700 btn-sm">
                    <i class="bi bi-patch-check me-1"></i>Terima Verifikasi Akun
                </button>
            </form>
            
            {{-- Reject --}}
            <button class="btn btn-outline-danger w-100 btn-sm"
                    onclick="toggleForm('reject', {{ $user->id }})">
                <i class="bi bi-x-circle me-1"></i>Tolak Pengajuan
            </button>
            <div class="d-none mt-2" id="reject-{{ $user->id }}">
                <form method="POST" action="{{ route('admin.verifications.reject', $user) }}">
                    @csrf
                    <textarea class="form-control form-control-sm mb-2" name="rejection_reason"
                              rows="3" placeholder="Alasan penolakan (wajib diisi)..." required></textarea>
                    <button type="submit" class="btn btn-danger w-100 btn-sm fw-700"
                            onclick="return confirm('Tolak pengajuan {{ $user->name }}?')">
                        Konfirmasi Tolak
                    </button>
                </form>
            </div>

        </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="card border-0 shadow-sm mb-5">
                <div class="card-body text-center py-4 text-muted">
            <i class="bi bi-clock" style="font-size:36px;"></i>
            <p class="small mt-2 mb-0">Tidak ada pengajuan verifikasi yang menunggu</p>
        </div>
    </div>
    @endif

        {{-- ===== APPROVED ===== --}}
    <div class="d-flex align-items-center gap-2 mb-3">
        <h5 class="fw-700 mb-0">Sudah Terverifikasi</h5>
        <span class="badge bg-success px-2">{{ $verified->count() }}</span>
    </div>

    @if($verified->count())
    <div class="row g-3 mb-5">
        @foreach($verified as $user)
        <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm p-3 h-100 border-success border-opacity-25">
            <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="d-flex align-items-center justify-content-center fw-800 text-white flex-shrink-0"
                         style="width:44px;height:44px;border-radius:50%;background:#16a34a;font-size:18px;">
                    {{ strtoupper(substr($user->name,0,1)) }}
                </div>
                <div>
                    <div class="fw-700">{{ $user->name }}</div>
                    <div class="text-muted small">{{ $user->email }}</div>
                    <div class="text-success" style="font-size:11px;">
                        <i class="bi bi-patch-check-fill me-1"></i>
                        {{ $user->verified_at?->format('d M Y') ?? '-' }}

                    </div>
                </div>
            </div>

            <div class="bg-light rounded-3 p-2 mb-3 small">
            <div class="bg-light rounded-3 p-2 mb-3 small text-muted">
            <div class="mb-1"><i class="bi bi-person-badge me-1"></i>{{ $user->verify_full_name ?? '-' }}</div>
            <div class="mb-1"><i class="bi bi-credit-card me-1"></i>{{ $user->verify_ktp_number ?? '-' }}</div>
            <div><i class="bi bi-collection me-1"></i>{{ $user->campaigns()->count() }} campaign</div>

            </div>

            {{-- Revoke --}}
            <button class="btn btn-outline-danger w-100 btn-sm"
                    onclick="toggleForm('revoke', {{ $user->id }})">
                <i class="bi bi-x-circle me-1"></i>Cabut Verifikasi
            </button>
            <div class="d-none mt-2" id="revoke-{{ $user->id }}">
                    <form method="POST" action="{{ route('admin.verifications.revoke', $user) }}">
                      {{-- onsubmit="return confirm('Cabut verifikasi {{ $user->name }}?')"> --}}
                    @csrf
                    <textarea class="form-control form-control-sm mb-2" name="rejection_reason"
                              rows="2" placeholder="Alasan pencabutan..." required></textarea>
                    <button type="submit" class="btn btn-danger w-100 btn-sm fw-700"
                            onclick="return confirm('Cabut verifikasi {{ $user->name }}?')">
                        Konfirmasi Cabut
                    </button>
                </form>
            </div>
        </div>
        </div>
        @endforeach
    </div>
        @endif

    {{-- ===== REJECTED ===== --}}
    @if($rejected->count())
    <div class="d-flex align-items-center gap-2 mb-3">
        <h5 class="fw-700 mb-0">Ditolak</h5>
        <span class="badge bg-danger px-2">{{ $rejected->count() }}</span>
    </div>
    <div class="row g-3 mb-5">
        @foreach($rejected as $user)
        <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm p-3 h-100">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="d-flex align-items-center justify-content-center fw-800 text-white flex-shrink-0"
                     style="width:44px;height:44px;border-radius:50%;background:#dc2626;font-size:18px;">
                    {{ strtoupper(substr($user->name,0,1)) }}
                </div>
                <div>
                    <div class="fw-700">{{ $user->name }}</div>
                    <div class="text-muted small">{{ $user->email }}</div>
                </div>
            </div>
            @if($user->rejection_reason)
            <div class="alert alert-danger py-2 px-2 small mb-2">
                <strong>Alasan ditolak:</strong> {{ $user->rejection_reason }}
            </div>
            @endif
            {{-- Bisa approve ulang --}}
            <form method="POST" action="{{ route('admin.verifications.approve', $user) }}"
                  onsubmit="return confirm('Verifikasi ulang akun {{ $user->name }}?')">
                @csrf
                <button type="submit" class="btn btn-outline-success w-100 btn-sm fw-700">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Verifikasi Ulang
                </button>
            </form>
        </div>
        </div>
        @endforeach
    </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleForm(type, id) {
    const el = document.getElementById(type + '-' + id);
    if (el) el.classList.toggle('d-none');
}
</script>
</body>
</html>
