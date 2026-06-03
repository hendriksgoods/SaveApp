<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – SaveThem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-light">

{{-- Admin Header --}}
<div class="bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
    <div>
        <h5 class="fw-800 mb-0">Admin Dashboard</h5>
        <p class="text-muted small mb-0">Verifikasi & Review Campaign</p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <span class="badge bg-primary-subtle text-primary px-3 py-2 fs-6">
            {{ $allCount }} Campaign Pending
        </span>
         <a href="{{ route('admin.verifications.index') }}" class="btn btn-outline-primary btn-sm fw-700">
            <i class="bi bi-patch-check me-1"></i>Verifikasi Penggalang
         </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-secondary btn-sm">Logout</button>
        </form>
        <a href="{{ route('home') }}" class="btn btn-light btn-sm border"><i class="bi bi-x-lg"></i></a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible m-0 rounded-0 border-0 border-start border-4 border-success py-2">
    <div class="container small"><i class="bi bi-check-circle-fill me-2 text-success"></i>{{ session('success') }}
    <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button></div>
</div>
@endif

<div class="container py-4">
    @if($pending->count())
        <div class="row g-3">
            @foreach($pending as $campaign)
            <div class="col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-warning-subtle text-warning fw-600">Pending Review</span>
                            <small class="text-muted">{{ $campaign->created_at->format('j/n/Y') }}</small>
                        </div>
                        <h6 class="fw-700 mb-2">{{ $campaign->title }}</h6>
                        <div class="text-muted small mb-1">
                            <i class="bi bi-person me-1"></i>{{ $campaign->user->name }}
                        </div>
                        <div class="text-muted small mb-1">
                            <i class="bi bi-geo-alt me-1"></i>{{ $campaign->location }}
                        </div>
                        <div class="text-primary fw-700 small mb-3">
                            <i class="bi bi-bullseye me-1"></i>Rp {{ number_format($campaign->target_amount, 0, ',', '.') }}
                        </div>
                        <a href="{{ route('admin.campaigns.review', $campaign) }}"
                           class="btn btn-primary w-100 btn-sm">
                            <i class="bi bi-eye me-1"></i>Review Campaign
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-clock text-muted" style="font-size:48px;"></i>
                <h5 class="fw-700 mt-3">Tidak Ada Campaign Pending</h5>
                <p class="text-muted small">Semua campaign telah direview</p>
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
