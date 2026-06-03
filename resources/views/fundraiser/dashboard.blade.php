@extends('layouts.app')
@section('title','Dashboard – SaveThem')

@section('content')


@if(!auth()->user()->is_verified)
@php $vs = auth()->user()->verification_status; @endphp
<div class="alert {{ $vs==='rejected' ? 'alert-danger' : 'alert-warning' }} m-0 rounded-0 border-0 py-3">
    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-{{ $vs==='rejected' ? 'x-circle-fill text-danger' : ($vs==='pending' ? 'clock-fill text-warning' : 'shield-exclamation text-warning') }} fs-5"></i>
            <div>
                @if($vs === 'rejected')
                    <strong>Pengajuan Verifikasi Ditolak</strong>
                    <div class="small text-muted">{{ auth()->user()->rejection_reason }}</div>
                @elseif($vs === 'pending')
                    <strong>Pengajuan Sedang Direview</strong>
                    <div class="small text-muted">Admin sedang mereview pengajuan verifikasi Anda (1-2 hari kerja).</div>
                @else
                    <strong>Akun Belum Terverifikasi</strong>
                    <div class="small text-muted">Ajukan verifikasi agar bisa membuat campaign.</div>
                @endif

            </div>
        </div>
        <a href="{{ route('verification.request') }}"
           class="btn btn-sm fw-700 {{ $vs==='rejected' ? 'btn-danger' : 'btn-warning' }}">
            <i class="bi bi-arrow-right me-1"></i>
            {{ $vs==='rejected' ? 'Ajukan Ulang' : ($vs==='pending' ? 'Lihat Status' : 'Ajukan Verifikasi') }}
        </a>
    </div>
</div>
@endif

<div class="bg-primary text-white py-4">
    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4 class="fw-800 mb-0">Halo, {{ auth()->user()->name }}! 👋</h4>
            <p class="mb-0 text-white text-opacity-75 small">Dashboard Penggalang Dana</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('profile.show') }}" class="btn btn-outline-light btn-sm fw-600">
                <i class="bi bi-person-circle me-1"></i>Profil
            </a>
            <a href="{{ route('campaigns.create') }}" class="btn btn-light fw-700 btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Buat Campaign Baru
            </a>
        </div>
    </div>
</div>

<div class="bg-light py-4">
<div class="container">

    {{-- Stats --}}
    @php
    $active   = $campaigns->where('status','active')->count();
    $pending  = $campaigns->where('status','pending')->count();
    $rejected = $campaigns->where('status','rejected')->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center">
                <div class="fw-800 fs-3 text-primary">{{ $campaigns->count() }}</div>
                <div class="text-muted small">Total Campaign</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center">
                <div class="fw-800 fs-3 text-success">{{ $active }}</div>
                <div class="text-muted small">Aktif</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center">
                <div class="fw-800 fs-3 text-warning">{{ $pending }}</div>
                <div class="text-muted small">Menunggu Review</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 text-center">
                <div class="fw-800 fs-3 {{ $rejected > 0 ? 'text-danger' : 'text-dark' }}">
                    {{ $rejected }}
                </div>
                <div class="text-muted small">Ditolak</div>
            </div>
        </div>
    </div>

    {{-- Campaign Tabs --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom px-3 pt-3 pb-0">
            <ul class="nav nav-tabs border-0" id="dashTabs">
                <li class="nav-item">
                    <button class="nav-link fw-600 dash-tab active" data-target="tabAll">
                        Semua
                        <span class="badge bg-secondary ms-1" style="font-size:10px;">
                            {{ $campaigns->count() }}
                        </span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-600 dash-tab" data-target="tabActive">
                        Aktif
                        <span class="badge bg-success ms-1" style="font-size:10px;">{{ $active }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-600 dash-tab" data-target="tabPending">
                        Review
                        <span class="badge bg-warning ms-1" style="font-size:10px;">{{ $pending }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-600 dash-tab" data-target="tabRejected" id="rejectedTab">
                        Ditolak
                        @if($rejected > 0)
                        <span class="badge bg-danger ms-1" style="font-size:10px;">{{ $rejected }}</span>
                        @endif
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-3">

            {{-- TAB: Semua --}}
            <div class="dash-panel" id="tabAll">
                @include('fundraiser._campaign_list', ['list' => $campaigns->values()])
            </div>

            {{-- TAB: Aktif --}}
            <div class="dash-panel d-none" id="tabActive">
                @include('fundraiser._campaign_list', ['list' => $campaigns->where('status','active')->values()])
            </div>

            {{-- TAB: Pending --}}
            <div class="dash-panel d-none" id="tabPending">
                @include('fundraiser._campaign_list', ['list' => $campaigns->where('status','pending')->values()])
            </div>

            {{-- TAB: Ditolak — auto-expand rejected campaign edit form --}}
            <div class="dash-panel d-none" id="tabRejected">
                @if($campaigns->where('status','rejected')->count())
                    @foreach($campaigns->where('status','rejected') as $c)
                    <div class="card border-danger border-opacity-50 mb-3" id="rejected-{{ $c->id }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-danger-subtle text-danger fw-600 mb-1">Ditolak</span>
                                    <h6 class="fw-700 mb-0">{{ $c->title }}</h6>
                                    <div class="text-muted small">{{ $c->category }} · {{ $c->location ?? '-' }}</div>
                                </div>
                                <small class="text-muted">{{ $c->updated_at->format('d M Y') }}</small>
                            </div>

                            {{-- Alasan penolakan --}}
                            @if($c->rejection_reason)
                            <div class="alert alert-danger py-2 px-3 small mb-3">
                                <strong><i class="bi bi-exclamation-circle me-1"></i>Alasan Penolakan Admin:</strong>
                                <div class="mt-1">{{ $c->rejection_reason }}</div>
                            </div>
                            @endif

                            {{-- Tombol edit & expand --}}
                            <div class="d-flex gap-2">
                                <button class="btn btn-warning btn-sm fw-700 flex-grow-1"
                                        onclick="toggleEditForm({{ $c->id }})">
                                    <i class="bi bi-pencil me-1"></i>Perbaiki & Kirim Ulang
                                </button>
                                <form method="POST" action="{{ route('campaigns.destroy',$c->slug) }}"
                                      onsubmit="return confirm('Hapus campaign ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>

                            {{-- Inline quick-edit form --}}
                            <div class="edit-form mt-3 d-none border-top pt-3" id="editForm-{{ $c->id }}">
                                <p class="text-muted small mb-3">
                                    <i class="bi bi-info-circle me-1 text-primary"></i>
                                    Perbaiki bagian yang ditolak lalu kirim ulang untuk direview admin.
                                </p>
                                <a href="{{ route('campaigns.edit',$c->slug) }}"
                                   class="btn btn-primary w-100 fw-700">
                                    <i class="bi bi-pencil-square me-1"></i>
                                    Buka Form Edit Campaign (7 Langkah)
                                </a>
                                <div class="text-center mt-2">
                                    <small class="text-muted">
                                        Setelah dikirim ulang, campaign akan kembali ke antrian review admin.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                        <p class="small">Tidak ada campaign yang ditolak</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

</div>
</div>
@endsection

@push('scripts')
<script>
// Tab switching
document.querySelectorAll('.dash-tab').forEach(btn => {
    btn.addEventListener('click', function(){
        document.querySelectorAll('.dash-tab').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.dash-panel').forEach(p => p.classList.add('d-none'));
        this.classList.add('active');
        document.getElementById(this.dataset.target).classList.remove('d-none');
    });
});

// Toggle edit form for rejected campaign
function toggleEditForm(id){
    const form = document.getElementById('editForm-'+id);
    if(form) form.classList.toggle('d-none');
}

// Auto-open rejected tab if URL has #rejected
if(window.location.hash === '#rejected'){
    document.getElementById('rejectedTab').click();
    // Auto-expand first rejected form
    const firstForm = document.querySelector('.edit-form');
    if(firstForm) firstForm.classList.remove('d-none');
}

// Auto-open rejected tab if there are rejected campaigns and coming from rejection
@if(session('open_rejected'))
    document.getElementById('rejectedTab')?.click();
@endif
</script>
@endpush
