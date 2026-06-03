<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Campaign – Admin SaveThem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-light">

<div class="bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between sticky-top shadow-sm">
    <div>
        <h5 class="fw-800 mb-0">Review Campaign</h5>
        <p class="text-muted small mb-0 text-truncate" style="max-width:400px;">{{ $campaign->title }}</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="container py-4">
<div class="row g-4">

    {{-- ===== LEFT: Full Campaign Detail ===== --}}
    <div class="col-lg-8">

        {{-- Kategori & Lokasi --}}
        <div class="card border-0 shadow-sm mb-3 p-3">
            <div class="row g-2 small">
                <div class="col-6">
                    <div class="text-muted fw-600 text-uppercase mb-1" style="font-size:10px;">Kategori</div>
                    <span class="badge bg-primary-subtle text-primary px-2 py-1">{{ $campaign->category }}</span>
                </div>
                <div class="col-6">
                    <div class="text-muted fw-600 text-uppercase mb-1" style="font-size:10px;">Lokasi</div>
                    <span class="fw-600">{{ $campaign->location }}</span>
                </div>
            </div>
        </div>

        {{-- Data Pribadi Penggalang --}}
        <div class="card border-0 shadow-sm mb-3 p-3">
            <h6 class="fw-700 mb-3 text-uppercase text-muted" style="font-size:11px;">Data Pribadi Penggalang Dana</h6>
            <div class="row g-2 small">
                @foreach([
                    ['Nama Akun',           $campaign->user->name],
                    ['Email',               $campaign->user->email],
                    ['Nama Sesuai KTP',     $campaign->full_name_ktp],
                    ['Nomor KTP',           $campaign->ktp_number],
                    ['No. Telepon',         $campaign->phone],
                    ['Pekerjaan',           $campaign->occupation],
                ] as [$label, $val])
                <div class="col-md-6">
                    <div class="text-muted" style="font-size:11px;">{{ $label }}</div>
                    <div class="fw-600">{{ $val ?: '—' }}</div>
                </div>
                @endforeach
            </div>
            @if($campaign->facebook || $campaign->instagram || $campaign->twitter)
            <hr class="my-2">
            <div class="small">
                <div class="text-muted fw-600 mb-1" style="font-size:11px;">Media Sosial</div>
                <div class="d-flex gap-3 flex-wrap">
                    @if($campaign->facebook)
                    <a href="{{ $campaign->facebook }}" target="_blank" class="text-primary small">
                        <i class="bi bi-facebook me-1"></i>Facebook
                    </a>
                    @endif
                    @if($campaign->instagram)
                    <a href="{{ $campaign->instagram }}" target="_blank" class="text-danger small">
                        <i class="bi bi-instagram me-1"></i>Instagram
                    </a>
                    @endif
                    @if($campaign->twitter)
                    <a href="{{ $campaign->twitter }}" target="_blank" class="text-info small">
                        <i class="bi bi-twitter-x me-1"></i>Twitter/X
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Detail Campaign --}}
        <div class="card border-0 shadow-sm mb-3 p-3">
            <h6 class="fw-700 mb-3 text-uppercase text-muted" style="font-size:11px;">Detail Campaign</h6>
            <h5 class="fw-800 mb-3">{{ $campaign->title }}</h5>

            <div class="mb-3">
                <div class="text-muted small fw-600 mb-1">Cerita Campaign</div>
                <div class="small" style="white-space:pre-line;line-height:1.7;">{{ $campaign->story }}</div>
            </div>

            <div class="mb-3">
                <div class="text-muted small fw-600 mb-1">Deskripsi Lengkap</div>
                <div class="small" style="white-space:pre-line;line-height:1.7;">{{ $campaign->description }}</div>
            </div>

            <div>
                <div class="text-muted small fw-600 mb-1">Tujuan Penggalangan Dana</div>
                <div class="small" style="white-space:pre-line;line-height:1.7;">{{ $campaign->fund_purpose }}</div>
            </div>
        </div>

        {{-- Pendanaan --}}
        <div class="card border-0 shadow-sm mb-3 p-3">
            <h6 class="fw-700 mb-3 text-uppercase text-muted" style="font-size:11px;">Target Pendanaan</h6>
            <div class="row g-2 small mb-3">
                <div class="col-6">
                    <div class="text-muted" style="font-size:11px;">Target Dana</div>
                    <div class="fw-700 text-primary fs-5">Rp {{ number_format($campaign->target_amount,0,',','.') }}</div>
                </div>
                <div class="col-6">
                    <div class="text-muted" style="font-size:11px;">Durasi</div>
                    <div class="fw-700">{{ $campaign->duration_days }} hari</div>
                </div>
            </div>
            <div class="text-muted small fw-600 mb-1">Rincian Penggunaan Dana</div>
            <div class="small bg-light rounded-3 p-2" style="white-space:pre-line;line-height:1.7;">{{ $campaign->fund_detail }}</div>
        </div>

        {{-- Alasan --}}
        <div class="card border-0 shadow-sm mb-3 p-3">
            <h6 class="fw-700 mb-3 text-uppercase text-muted" style="font-size:11px;">Alasan Menggalang Dana</h6>
            <div class="small" style="white-space:pre-line;line-height:1.85;">{{ $campaign->reason }}</div>
        </div>

    </div>

    {{-- ===== RIGHT: Admin Actions ===== --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 sticky-top" style="top:80px;">
            <h6 class="fw-700 mb-1">Keputusan Admin</h6>
            <p class="text-muted small mb-3">Setujui atau tolak campaign ini</p>

            {{-- Approve --}}
            <form method="POST" action="{{ route('admin.campaigns.approve', $campaign) }}" class="mb-3"
                  onsubmit="return confirm('Setujui campaign ini? Campaign akan langsung aktif dan tampil di homepage.')">
                @csrf
                <button type="submit" class="btn btn-success w-100 py-2 fw-700">
                    <i class="bi bi-check-circle me-2"></i>Setujui Campaign
                </button>
            </form>

            <div class="d-flex align-items-center gap-2 text-muted small mb-3">
                <hr class="flex-grow-1 m-0"><span>atau</span><hr class="flex-grow-1 m-0">
            </div>

            {{-- Reject --}}
            <form method="POST" action="{{ route('admin.campaigns.reject', $campaign) }}"
                  onsubmit="return confirmReject(event)">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-600 small">
                        Alasan Penolakan <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('rejection_reason') is-invalid @enderror"
                              name="rejection_reason" id="rejectionReason" rows="5">
                            {{ old('rejection_reason') }}</textarea>
                    @error('rejection_reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                <button type="submit" class="btn btn-danger w-100 py-2 fw-700">
                    <i class="bi bi-x-circle me-2"></i>Tolak Campaign
                </button>
            </form>
        </div>
    </div>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmReject(e) {
    const reason = document.getElementById('rejectionReason').value.trim();
    if (!reason) {
        e.preventDefault();
        document.getElementById('rejectionReason').classList.add('is-invalid');
        document.getElementById('rejectionReason').focus();
        return false;
    }
    return confirm('Tolak campaign ini? Penggalang dana akan melihat alasan penolakan dan bisa mengedit serta mengirim ulang.');
}
</script>
</body>
</html>
