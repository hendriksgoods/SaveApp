@extends('layouts.app')
@section('title', $campaign->title . ' – SaveThem')

@section('content')
<div class="bg-light py-4">
<div class="container">

{{-- ===== HEADER ===== --}}
<div class="card border-0 shadow-sm mb-4 p-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
        <div class="flex-grow-1">
            <h2 class="fw-800 mb-1">{{ $campaign->title }}</h2>
            <p class="text-muted small mb-0">Oleh {{ $campaign->user->name }}</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                <i class="bi bi-patch-check me-1"></i>Terverifikasi
            </span>
            <a href="{{ route('campaigns.index') }}" class="btn btn-sm btn-light border">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </div>

    {{-- Stats strip --}}
    <div class="row g-3 mb-3">
        <div class="col-4">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-heart-fill text-danger fs-5"></i>
                <div>
                    <div class="fw-800">{{ number_format($campaign->donations->count()) }}</div>
                    <div class="text-muted" style="font-size:11px;">Donatur</div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-clock text-warning fs-5"></i>
                <div>
                    <div class="fw-800">{{ $campaign->days_left }}</div>
                    <div class="text-muted" style="font-size:11px;">Hari Lagi</div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-graph-up-arrow text-success fs-5"></i>
                <div>
                    <div class="fw-800 text-success">Aktif</div>
                    <div class="text-muted" style="font-size:11px;">Status</div>
                </div>
            </div>
        </div>
    </div>

    
    {{-- Progress --}}
<div class="d-flex justify-content-between align-items-center small mb-1">
    <span class="fw-700 {{ $campaign->percentage >= 100 ? 'text-success' : 'text-primary' }}">
        {{ $campaign->formatted_raised }}
    </span>
    <span class="text-muted">
        dari {{ $campaign->formatted_target }}
        <span class="fw-700 {{ $campaign->percentage >= 100 ? 'text-success' : 'text-dark' }}">
            ({{ $campaign->percentage }}%)
        </span>
        @if($campaign->percentage >= 100)
            <span class="badge bg-success ms-1" style="font-size:10px;">Target Tercapai!</span>
        @endif
    </span>
</div>
<div class="progress" style="height:8px;border-radius:99px;">
    <div class="progress-bar {{ $campaign->percentage >= 100 ? 'bg-success' : 'bg-primary' }}"
         style="width:{{ min(100, $campaign->percentage) }}%;border-radius:99px;">
    </div>
</div>

<div class="row g-4">

{{-- ===== LEFT: Tabs ===== --}}
<div class="col-lg-8">
<div class="card border-0 shadow-sm">

    {{-- Tab Navigation --}}
    <div class="border-bottom px-3">
        <ul class="nav nav-tabs border-0" id="campaignTabs">
            @foreach(['informasi'=>'Informasi','galeri'=>'Galeri','transparansi'=>'Laporan Dana','penarikan'=>'Riwayat Penarikan','forum'=>'Forum','jejak'=>'Jejak Kebaikan',] as $key=>$label)
            <li class="nav-item">
                <button class="nav-link fw-600 campaign-tab {{ $tab===$key ? 'active' : '' }}"
                        data-tab="{{ $key }}">{{ $label }}</button>
            </li>
            @endforeach
        </ul>
    </div>

    <div class="p-4">

    {{-- ============ TAB: INFORMASI ============ --}}
    <div class="tab-content-panel {{ $tab==='informasi' ? '' : 'd-none' }}" id="tab-informasi">
        @if($campaign->image)
        <img src="{{ asset('storage/'.$campaign->image) }}"
             class="w-100 rounded-3 mb-4" style="max-height:320px;object-fit:cover;" alt="{{ $campaign->title }}">
        @endif

        <h5 class="fw-700 mb-2">Tentang Campaign</h5>
        <p class="text-muted mb-4" style="line-height:1.8;">{{ $campaign->description }}</p>

        <h5 class="fw-700 mb-2">Tujuan Penggalangan Dana</h5>
        <ul class="list-unstyled mb-4">
            @foreach(array_filter(explode("\n", $campaign->fund_purpose)) as $point)
            <li class="d-flex align-items-start gap-2 mb-2">
                <span class="text-primary mt-1" style="font-size:8px;">●</span>
                <span class="text-muted small">{{ trim($point) }}</span>
            </li>
            @endforeach
        </ul>

        <h5 class="fw-700 mb-2">Cerita Lengkap</h5>
        <div class="text-muted small" style="white-space:pre-line;line-height:1.85;">{{ $campaign->story }}</div>
    </div>

    {{-- ============ TAB: GALERI ============ --}}
    <div class="tab-content-panel d-none" id="tab-galeri">
        <h5 class="fw-700 mb-3">Galeri Foto</h5>

        {{-- Upload form — hanya penggalang pemilik --}}
        @auth
        @if(auth()->id() === $campaign->user_id)
        <div class="card bg-light border-dashed mb-4 p-3">
            <form action="{{ route('gallery.store', $campaign->slug) }}" method="POST"
                  enctype="multipart/form-data" id="galleryForm">
                @csrf
                <label class="form-label fw-600 small">Upload Foto Baru</label>
                <div class="upload-zone mb-2" id="galleryUpload" onclick="document.getElementById('galleryInput').click()">
                    <i class="bi bi-cloud-upload fs-3 text-muted d-block mb-1"></i>
                    <span class="text-muted small">Klik atau seret foto ke sini</span>
                    <span class="text-muted d-block" style="font-size:11px;">JPG, PNG, WebP — maks. 3MB per foto</span>
                    <input type="file" id="galleryInput" name="images[]" multiple accept="image/*" style="display:none"
                           onchange="previewGallery(this)">
                </div>
                <div id="galleryPreviews" class="row g-2 mb-2"></div>
                <button type="submit" class="btn btn-primary btn-sm fw-700">
                    <i class="bi bi-cloud-upload me-1"></i>Upload Foto
                </button>
            </form>
        </div>
        @endif
        @endauth

        {{-- Gallery Grid --}}
        @if($campaign->galleries->count())
        <div class="row g-2">
            @foreach($campaign->galleries as $photo)
            <div class="col-6 col-md-4 position-relative gallery-item">
                <img src="{{ asset('storage/'.$photo->image_path) }}"
                     class="img-fluid rounded-3 w-100 gallery-thumb"
                     style="height:160px;object-fit:cover;cursor:pointer;"
                     alt="Galeri"
                     onclick="openLightbox('{{ asset('storage/'.$photo->image_path) }}')">
                @auth
                @if(auth()->id() === $campaign->user_id)
                <form method="POST"
                      action="{{ route('gallery.destroy', [$campaign->slug, $photo->id]) }}"
                      onsubmit="return confirm('Hapus foto ini?')"
                      class="position-absolute top-0 end-0 m-1">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm rounded-circle gallery-del"
                            style="width:28px;height:28px;padding:0;font-size:12px;">
                        <i class="bi bi-x"></i>
                    </button>
                </form>
                @endif
                @endauth
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-images fs-1 d-block mb-2"></i>
            <p class="small">Belum ada foto di galeri</p>
        </div>
        @endif
    </div>

    {{-- ============ TAB: TRANSPARANSI ============ --}}
    <div class="tab-content-panel d-none" id="tab-transparansi">
        <h5 class="fw-700 mb-3">Laporan Penggunaan Dana</h5>

        {{-- Ringkasan --}}
        <div class="card border-0 mb-4 p-3" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);">
            <h6 class="fw-700 mb-3">Ringkasan Penggunaan Dana</h6>
            <div class="row g-3 mb-3">
                <div class="col-4">
                    <div class="text-muted small">Dana Terkumpul</div>
                    <div class="fw-800 text-primary" style="font-size:15px;">{{ $campaign->formatted_raised }}</div>
                </div>
                <div class="col-4">
                    <div class="text-muted small">Dana Terpakai</div>
                    <div class="fw-800 text-success" style="font-size:15px;">
                        Rp {{ number_format($totalUsed,0,',','.') }}
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-muted small">Sisa Dana</div>
                    <div class="fw-800" style="font-size:15px;">
                        Rp {{ number_format($totalSisa,0,',','.') }}
                    </div>
                </div>
            </div>
            @php
                $overallPct = $campaign->raised_amount > 0
                    ? min(100, round(($totalUsed / $campaign->raised_amount) * 100, 1))
                    : 0;
            @endphp
            <div class="d-flex justify-content-between small mb-1">
                <span class="fw-600">Progress Keseluruhan</span>
                <span class="text-primary fw-700">{{ $overallPct }}%</span>
            </div>
            <div class="progress" style="height:10px;border-radius:99px;">
                <div class="progress-bar bg-primary" style="width:{{ $overallPct }}%;border-radius:99px;">
                    <span class="small px-1">{{ $overallPct }}%</span>
                </div>
            </div>
        </div>

        {{-- Tambah Laporan — hanya penggalang pemilik --}}
        @auth
        @if(auth()->id() === $campaign->user_id)
        <div class="card border-0 bg-light mb-4 p-3">
            <h6 class="fw-700 mb-3">+ Tambah Laporan Penggunaan Dana</h6>
            <form action="{{ route('fund_usage.store', $campaign->slug) }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <div class="row g-2 mb-2">
                    <div class="col-12">
                        <label class="form-label fw-600 small">Judul <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" name="title" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-600 small">Keterangan</label>
                        <input type="text" class="form-control form-control-sm" name="description">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600 small">Anggaran (Rp)</label>
                        <input type="text" class="form-control form-control-sm" name="budget"
                               id="budgetInput"
                               placeholder="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600 small">Terpakai (Rp)</label>
                        <input type="text" class="form-control form-control-sm" name="used"
                               id="usedInput"
                               placeholder="0" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600 small">Tanggal</label>
                        <input type="date" class="form-control form-control-sm" name="usage_date"
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600 small">Status</label>
                        <select class="form-select form-select-sm" name="status">
                            <option value="ongoing">Berlangsung</option>
                            <option value="done">Selesai</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-600 small">Bukti Pengunaan Dalam Bentuk Foto</label>
                        <input type="file" class="form-control form-control-sm" name="proofs[]"
                               multiple accept="image/*">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm fw-700">
                    <i class="bi bi-plus-lg me-1"></i>Simpan Laporan
                </button>
            </form>
        </div>
        @endif
        @endauth

        {{-- Detail Penggunaan Dana --}}
        <h6 class="fw-700 mb-3">Detail Penggunaan Dana</h6>
        @if($campaign->fundUsages->count())
            @foreach($campaign->fundUsages->sortByDesc('usage_date') as $usage)
            <div class="card border-0 shadow-sm mb-3 p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="fw-700">{{ $usage->title }}</span>
                        <span class="badge {{ $usage->status==='done' ? 'bg-success' : 'bg-warning text-dark' }} px-2 py-1" style="font-size:11px;">
                            @if($usage->status==='done')
                                <i class="bi bi-check-circle me-1"></i>Selesai
                            @else
                                Berlangsung
                            @endif
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="text-end">
                            <div class="text-muted" style="font-size:11px;">Anggaran</div>
                            <div class="fw-700 small">{{ $usage->formatted_budget }}</div>
                        </div>
                        @auth
                        @if(auth()->id() === $campaign->user_id)
                        <form method="POST"
                              action="{{ route('fund_usage.destroy', [$campaign->slug, $usage->id]) }}"
                              onsubmit="return confirm('Hapus laporan ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm py-0" style="font-size:11px;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                        @endauth
                    </div>
                </div>

                @if($usage->description)
                <p class="text-muted small mb-2">{{ $usage->description }}</p>
                @endif

                @if($usage->usage_date)
                <div class="text-muted small mb-2">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $usage->usage_date->format('d F Y') }}
                </div>
                @endif

                {{-- Progress bar penggunaan --}}
                <div class="d-flex justify-content-between small mb-1">
                    <span class="text-muted">Penggunaan Dana</span>
                    <span class="fw-600">
                        {{ $usage->formatted_used }} / {{ $usage->formatted_budget }}
                    </span>
                </div>
                <div class="progress mb-1" style="height:8px;border-radius:99px;">
                    <div class="progress-bar bg-success" style="width:{{ $usage->percentage }}%;border-radius:99px;"></div>
                </div>
                <div class="small text-muted mb-3">{{ $usage->percentage }}% terpakai</div>

                {{-- Bukti foto --}}
                @if($usage->proofs->count())
                <div class="mb-1">
                    <div class="small fw-600 mb-2">
                        <i class="bi bi-receipt me-1"></i>Bukti Transfer ({{ $usage->proofs->count() }})
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        @foreach($usage->proofs as $proof)
                        <div class="position-relative">
                            <img src="{{ asset('storage/'.$proof->image_path) }}"
                                 class="rounded-2 border"
                                 style="width:80px;height:80px;object-fit:cover;cursor:pointer;"
                                 onclick="openLightbox('{{ asset('storage/'.$proof->image_path) }}')"
                                 alt="Bukti">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-bar-chart fs-1 d-block mb-2"></i>
                <p class="small">Belum ada laporan penggunaan dana</p>
            </div>
        @endif
    </div>

        {{-- ============ TAB: PENARIKAN ============ --}}
            <div class="tab-content-panel d-none" id="tab-penarikan">
            <h5 class="fw-700 mb-3">Riwayat Penarikan Dana</h5>
                @if($campaign->withdrawals->count())
        @foreach($campaign->withdrawals as $withdrawal)
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-700 text-success">
                        Rp {{ number_format($withdrawal->amount,0,',','.') }}
                    </span>
                    <small class="text-muted">
                        {{ $withdrawal->processed_at
                            ? $withdrawal->processed_at->format('d M Y')
                            : $withdrawal->created_at->format('d M Y') }}
                    </small>
                </div>
                @if($withdrawal->description)
                <p class="text-muted small mb-0">
                    {{ $withdrawal->description }}
                </p>
                @endif

            </div>
        </div>
        @endforeach
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-wallet2 fs-1 d-block mb-2"></i>
            <p class="small mb-0">
                Belum ada riwayat penarikan dana
            </p>
        </div>
    @endif

</div>

    {{-- ============ TAB: FORUM ============ --}}
    <div class="tab-content-panel d-none" id="tab-forum">
        <h5 class="fw-700 mb-1">Diskusi & Pertanyaan</h5>
        <p class="text-muted small mb-3">Tanyakan kepada penggalang dana atau berikan dukungan kepada campaign ini</p>

        {{-- Form komentar — semua user login bisa komentar --}}
        @auth
        @if(auth()->user()->isDonatur() || auth()->user()->isPenggalang())
        <form action="{{ route('forum.store', $campaign->slug) }}" method="POST" class="mb-4">
            @csrf
            <textarea class="form-control mb-2 @error('body') is-invalid @enderror"
                      name="body" rows="3"
                      placeholder="Tulis komentar atau pertanyaan Anda..."></textarea>
            @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary fw-700 px-4">
                    <i class="bi bi-send me-1"></i>Kirim
                </button>
            </div>
        </form>
        @endif
        @else
        <div class="alert alert-light border text-center small mb-4">
            <a href="{{ route('login') }}" class="text-primary fw-700">Login</a> untuk berkomentar
        </div>
        @endauth

        {{-- Comments list --}}
        @forelse($comments as $comment)
        <div class="mb-4" id="comment-{{ $comment->id }}">
            {{-- Main comment --}}
            <div class="d-flex gap-3">
                <div class="avatar-sm flex-shrink-0">
                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <span class="fw-700 small">{{ $comment->user->name }}</span>
                        <span class="badge px-2 py-1"
                              style="font-size:10px;
                              {{ $comment->user->isPenggalang() ? 'background:#f0fdf4;color:#16a34a;' : 'background:#eff6ff;color:#2563eb;' }}">
                            <i class="bi bi-{{ $comment->user->isPenggalang() ? 'person-check' : 'heart' }} me-1"></i>
                            {{ $comment->user->isPenggalang() ? 'Penggalang Dana' : 'Donatur' }}
                        </span>
                        <span class="text-muted" style="font-size:11px;">
                            {{ $comment->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <p class="text-dark small mb-2" style="line-height:1.6;">{{ $comment->body }}</p>

                    {{-- Like & Balas --}}
                    <div class="d-flex align-items-center gap-3">
                        <form method="POST" action="{{ route('forum.like', [$campaign->slug, $comment->id]) }}">
                            @csrf
                            <button class="btn btn-link p-0 text-muted small text-decoration-none">
                                <i class="bi bi-heart me-1"></i>{{ $comment->likes }}
                            </button>
                        </form>

                        {{-- Hanya penggalang pemilik campaign yang bisa balas --}}
                        @auth
                        @if(auth()->id() === $campaign->user_id)
                        <button class="btn btn-link p-0 text-muted small text-decoration-none"
                                onclick="toggleReply({{ $comment->id }})">
                            <i class="bi bi-chat me-1"></i>Balas ({{ $comment->replies_count }})
                        </button>
                        @else
                        <span class="text-muted small">
                            <i class="bi bi-chat me-1"></i>{{ $comment->replies_count }} balasan
                        </span>
                        @endif
                        @endauth
                    
                        @auth
                                @if(auth()->id() === $campaign->user_id)
                        <form method="POST"
                                    action="{{ route('forum.destroy', [$campaign->slug, $comment->id]) }}"
                                    onsubmit="return confirm('Hapus komentar ini?')"
                                    class="d-inline">
                        @csrf
                        @method('DELETE')
                                <button class="btn btn-link p-0 text-danger small text-decoration-none">
                            <i class="bi bi-trash me-1"></i>Hapus
                        </button>
                    </form>
                        @endif
                        @endauth
                    </div>

                    {{-- Reply form (hidden by default) --}}
                    @auth
                    @if(auth()->id() === $campaign->user_id)
                    <div class="reply-form mt-2 d-none" id="reply-{{ $comment->id }}">
                        <form action="{{ route('forum.reply', [$campaign->slug, $comment->id]) }}" method="POST">
                            @csrf
                            <div class="d-flex gap-2">
                                <textarea class="form-control form-control-sm" name="body" rows="2"
                                          placeholder="Tulis balasan..." required></textarea>
                                <button type="submit" class="btn btn-primary btn-sm px-3 fw-700 align-self-end">
                                    Kirim
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                    @endauth

                    {{-- Replies --}}
                    @if($comment->replies->count())
                    <div class="mt-3 ps-3 border-start border-2">
                        @foreach($comment->replies as $reply)
                        <div class="d-flex gap-2 mb-3">
                            <div class="avatar-xs flex-shrink-0">
                                {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span class="fw-700 small">{{ $reply->user->name }}</span>
                                    <span class="badge px-2 py-1"
                                          style="font-size:10px;background:#f0fdf4;color:#16a34a;">
                                        <i class="bi bi-person-check me-1"></i>Penggalang Dana
                                    </span>
                                    <span class="text-muted" style="font-size:11px;">
                                        {{ $reply->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-dark small mb-1" style="line-height:1.6;">{{ $reply->body }}</p>
                            @auth
                                @if(auth()->id() === $campaign->user_id)
                                <form method="POST"
                                      action="{{ route('forum.destroyReply', [$campaign->slug, $reply->id]) }}"
                                      onsubmit="return confirm('Hapus balasan ini?')"
                                      class="d-inline">
                                      @csrf
                                   @method('DELETE')
                            <button class="btn btn-link p-0 text-danger small text-decoration-none">
                        <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                </form>
                        @endif
                        @endauth
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            <hr class="my-3">
        </div>
        @empty
        <div class="text-center py-4 text-muted">
            <i class="bi bi-chat-dots fs-1 d-block mb-2"></i>
            <p class="small">Belum ada komentar. Jadilah yang pertama!</p>
        </div>
        @endforelse
    </div>


    {{-- ============ TAB: JEJAK KEBAIKAN ============ --}}
    <div class="tab-content-panel {{ $tab==='jejak' ? '' : 'd-none' }}" id="tab-jejak">
        <h5 class="fw-700 mb-1">Jejak Kebaikan</h5>
        <p class="text-muted small mb-3">Dokumentasi perjalanan campaign dan penggunaan dana</p>

        {{-- Upload form — hanya penggalang pemilik --}}
        @auth
        @if(auth()->id() === $campaign->user_id)
        <div class="card bg-light border-0 mb-4 p-3">
            <h6 class="fw-700 mb-3">+ Tambah Jejak Kebaikan</h6>
            <form action="{{ route('updates.store', $campaign->slug) }}" method="POST"
                  enctype="multipart/form-data">
                @csrf
                <div class="mb-2">
                    <label class="form-label fw-600 small">Judul <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-sm" name="title"required>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-600 small">Deskripsi <span class="text-danger">*</span></label>
                    <textarea class="form-control form-control-sm" name="description" rows="3"required></textarea>
                </div>
                <div class="mb-3">
    <label class="form-label fw-600 small">Tanggal <span class="text-danger">*</span></label>
    <input type="date" class="form-control form-control-sm" name="update_date"
           value="{{ date('Y-m-d') }}" required>
    </div>
        <div class="mb-3">
    <label class="form-label fw-600 small">Foto</label>
    <input type="file" class="form-control form-control-sm" name="image_path"
           accept="image/*">
    </div>
    <button type="submit" class="btn btn-primary btn-sm fw-700">
            <i class="bi bi-plus-lg me-1"></i>Tambahkan
    </button>
            </form>
        </div>
        @endif
        @endauth

        {{-- List updates --}}
        @if($campaign->updates->count())
            @foreach($campaign->updates as $upd)
            <div class="card border-0 shadow-sm mb-3 overflow-hidden">
                @if($upd->image_path)
                <img src="{{ asset('storage/'.$upd->image_path) }}"
                     class="w-100" style="max-height:220px;object-fit:cover;"
                     alt="{{ $upd->title }}"
                     onclick="openLightbox('{{ asset('storage/'.$upd->image_path) }}')"
                     style="cursor:pointer;">
                @endif
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h6 class="fw-700 mb-0">{{ $upd->title }}</h6>
                        <div class="d-flex align-items-center gap-2">
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $upd->update_date ? $upd->update_date->format('d M Y') : $upd->created_at->format('d M Y') }}
                            </small>
                            @auth
                            @if(auth()->id() === $campaign->user_id)
                            <form method="POST"
                                  action="{{ route('updates.destroy',[$campaign->slug,$upd->id]) }}"
                                  onsubmit="return confirm('Hapus jejak ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm py-0 px-1" style="font-size:11px;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                            @endauth
                        </div>
                    </div>
                    <p class="text-muted small mb-0" style="line-height:1.7;">{{ $upd->description }}</p>
                </div>
            </div>
            @endforeach
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-journal-richtext fs-1 d-block mb-2"></i>
                <p class="small">Belum ada jejak kebaikan yang dibagikan</p>
            </div>
        @endif
    </div>

    </div>{{-- end p-4 --}}
</div>{{-- end card --}}
</div>{{-- end col-lg-8 --}}

{{-- ===== RIGHT: Sidebar ===== --}}
<div class="col-lg-4">

    {{-- Donate box --}}
    <div class="card border-0 shadow-sm p-4 mb-3 sticky-top" style="top:76px;">
        @php
            $isActiveCampaign = $campaign->status === 'active' && $campaign->days_left > 0;
            $isDonaturUser    = !auth()->check() || auth()->user()->isDonatur();
            $isBlockedRole    = auth()->check() && (auth()->user()->isPenggalang() || auth()->user()->isAdmin());
        @endphp

        @if($isActiveCampaign && $isDonaturUser)
        {{-- ✅ Form donasi — hanya tampil untuk donatur atau tamu --}}
        <form action="{{ route('donations.store', $campaign->slug) }}" method="POST">
            @csrf
            <h6 class="fw-700 mb-3">Donasi Sekarang</h6>
            <div class="d-flex gap-2 flex-wrap mb-2">
                @foreach([10000,25000,50000,100000] as $amt)
                <button type="button" class="btn btn-outline-primary btn-sm quick-amt"
                        data-amount="{{ $amt }}">
                    Rp {{ number_format($amt/1000) }}rb
                </button>
                @endforeach
            </div>
            <div class="input-group mb-2">
                <span class="input-group-text fw-600">Rp</span>
                <input type="text" class="form-control" name="amount" id="donateAmount"
                       placeholder="Minimal 10.000" required>
            </div>
            <input type="text" class="form-control mb-2" name="donor_name"
                   value="{{ auth()->check() ? auth()->user()->name : '' }}"
                   placeholder="Nama Anda" required>
            <input type="email" class="form-control mb-2" name="donor_email"
                   value="{{ auth()->check() ? auth()->user()->email : '' }}"
                   placeholder="Email Anda" required>
            <textarea class="form-control mb-2" name="message" rows="2"
                      placeholder="Pesan semangat (opsional)..."></textarea>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="anon" name="is_anonymous" value="1">
                <label class="form-check-label small text-muted" for="anon">Donasi anonim</label>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-700 py-2">
                <i class="bi bi-heart-fill me-1"></i>Donasi Sekarang
            </button>
            <p class="text-center text-muted mt-2 mb-0" style="font-size:11px;">
                🔒 Transaksi aman & terpercaya
            </p>
        </form>

        @elseif($isBlockedRole)
        {{-- ❌ Penggalang & Admin — tidak bisa donasi, tidak tampil form --}}

        @elseif(!$isActiveCampaign)
        {{-- Campaign tidak aktif --}}
        <div class="alert alert-secondary text-center fw-600 mb-0 small">
            Campaign sudah berakhir.
        </div>
        @endif
        
        {{-- Tombol Penarikan Dana --}}
        @auth
            @if(auth()->id() === $campaign->user_id)
        <div class="mb-3">
        <a href="{{ route('withdrawals.index', $campaign->slug) }}"
           class="btn btn-success w-100 fw-700 py-2">
        <i class="bi bi-wallet2 me-1"></i>
        Tarik Dana
        </a>
        </div>
        @endif
        @endauth
        {{-- Penggalang info --}}
        <hr class="my-3">
        <p class="fw-700 small mb-2">Penggalang Dana</p>
        <div class="d-flex align-items-center gap-2 mb-2">
            <div class="avatar-sm">{{ strtoupper(substr($campaign->user->name,0,1)) }}</div>
            <div>
                <div class="fw-700 small">{{ $campaign->user->name }}</div>
                <div class="text-primary" style="font-size:11px;">
                    <i class="bi bi-patch-check-fill me-1"></i>Akun Terverifikasi
                </div>
            </div>
        </div>
        <div class="text-muted small mb-1">
            <i class="bi bi-calendar3 me-1"></i>
            Bergabung sejak {{ $campaign->user->created_at->translatedFormat('F Y') }}
        </div>
        <div class="text-muted small">
            <i class="bi bi-graph-up me-1"></i>
            {{ $campaign->user->campaigns()->where('status','active')->count() }} Campaign Aktif
        </div>

        {{-- Dukungan Donatur --}}
        @php
            $donorMessages = $campaign->donations
                ->where('payment_status','paid')
                ->take(5);
        @endphp
        @if($donorMessages->count())
        <div class="card border-0 bg-light mt-3 p-3">
            <p class="fw-700 small mb-2">
                <i class="bi bi-heart-fill text-danger me-1"></i>Dukungan Donatur
            </p>
            @foreach($donorMessages as $don)
            <div class="d-flex gap-2 mb-2">
                <div class="avatar-xs flex-shrink-0"
                     style="background:#F97316;">
                    {{ strtoupper(substr($don->is_anonymous ? 'A' : $don->donor_name, 0, 1)) }}
                </div>
                <div>
                    <div class="fw-600" style="font-size:12px;">
                        {{ $don->is_anonymous ? 'Seseorang Telah Berdonasi Sebesar' 
                        :  $don->donor_name.     ' Berdonasi Sebesar'               }}
                        <span class="text-primary fw-700">{{ $don->formatted_amount }}</span>
                    </div>
                    @if($don->message)
                    <div class="text-muted" style="font-size:11px;line-height:1.4;">
                        "{{ $don->message }}"
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Security badge --}}
        <div class="card bg-success-subtle border-0 mt-3 p-3">
            <p class="fw-700 small mb-2 text-success">Keamanan Terjamin</p>
            @foreach(['Campaign diverifikasi oleh tim kami','Dana langsung ke rekening terverifikasi','Laporan transparan berkala'] as $item)
            <div class="d-flex align-items-center gap-2 small text-muted mb-1">
                <i class="bi bi-check-lg text-success"></i>{{ $item }}
            </div>
            @endforeach
        </div>
    </div>
</div>

</div>{{-- end row --}}
</div>{{-- end container --}}
</div>{{-- end bg-light --}}

{{-- Lightbox --}}
<div class="modal fade" id="lightboxModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 text-center">
                <img id="lightboxImg" src="" alt="" class="img-fluid rounded-3">
                <button type="button" class="btn btn-light btn-sm position-absolute top-0 end-0 m-2"
                        data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ---- Tab switching ----
document.querySelectorAll('.campaign-tab').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.campaign-tab').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content-panel').forEach(p => p.classList.add('d-none'));
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.remove('d-none');
        // Update URL without reload
        const url = new URL(window.location);
        url.searchParams.set('tab', this.dataset.tab);
        history.pushState({}, '', url);
    });
});

// ---- Quick donate amounts ----
document.querySelectorAll('.quick-amt').forEach(btn => {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.quick-amt').forEach(b => b.classList.remove('active','btn-primary'));
        this.classList.add('active','btn-primary');
        this.classList.remove('btn-outline-primary');
        document.getElementById('donateAmount').value = this.dataset.amount;
    });
});

// ---- Toggle reply form ----
function toggleReply(id) {
    const form = document.getElementById('reply-' + id);
    if (form) form.classList.toggle('d-none');
}

// ---- Lightbox ----
function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    new bootstrap.Modal(document.getElementById('lightboxModal')).show();
}

// ---- Gallery preview ----
function previewGallery(input) {
    const container = document.getElementById('galleryPreviews');
    container.innerHTML = '';
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const col = document.createElement('div');
            col.className = 'col-4 col-md-3';
            col.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded-2"
                              style="height:70px;object-fit:cover;width:100%;">`;
            container.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
}

// ---- Format Rupiah Input ----
function formatRupiah(input) {
    input.addEventListener('input', function () {
        let value = this.value.replace(/\D/g, '');

        if (value.length > 0) {
            this.value = new Intl.NumberFormat('id-ID').format(value);
        } else {
            this.value = '';
        }
    });
}

formatRupiah(document.getElementById('donateAmount'));
formatRupiah(document.getElementById('budgetInput'));
formatRupiah(document.getElementById('usedInput'));

// Auto-open correct tab from flash (forum)
@if(session('success') && str_contains(session('success'), 'Komentar'))
    document.querySelector('[data-tab="forum"]').click();
@endif
</script>
@endpush
