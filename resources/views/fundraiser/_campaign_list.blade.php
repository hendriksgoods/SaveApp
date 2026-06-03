{{-- Reusable campaign list partial --}}
@if($list->count())
<div class="row g-3">
@foreach($list as $c)
<div class="col-12 col-md-6">
<div class="card border-0 bg-light h-100 p-3">
    <div class="d-flex justify-content-between align-items-start mb-2">
          @if($c->status === 'active')
            <span class="badge fw-600 px-2 py-1" style="font-size:11px;background:#f0fdf4;color:#16a34a;">Aktif</span>
        @elseif($c->status === 'pending')
            <span class="badge fw-600 px-2 py-1" style="font-size:11px;background:#fffbeb;color:#d97706;">Review</span>
        @elseif($c->status === 'rejected')
            <span class="badge fw-600 px-2 py-1" style="font-size:11px;background:#fef2f2;color:#dc2626;">Ditolak</span>
        @elseif($c->status === 'completed')
            <span class="badge fw-600 px-2 py-1" style="font-size:11px;background:#eff6ff;color:#2563eb;">Selesai</span>
        @endif
            {{ match($c->status){
                'active'    =>'Aktif',
                'pending'   =>'Review',
                'rejected'  =>'Ditolak',
                'completed' =>'Selesai',
                default     => $c->status
            } }}
        </span>
        <small class="text-muted">{{ $c->created_at->format('d M Y') }}</small>
    </div>
    <h6 class="fw-700 mb-1">{{ $c->title }}</h6>
    <p class="text-muted small mb-2">{{ $c->category }} · {{ $c->location ?? '-' }}</p>

    @if($c->status==='active')
    <div class="progress mb-1" style="height:4px;border-radius:99px;">
        <div class="progress-bar {{ $c->percentage >= 100 ? 'bg-success' : 'bg-primary' }}"
             style="width:{{ min(100,$c->percentage) }}%;border-radius:99px;">
        </div>
    </div>
    <div class="d-flex justify-content-between small text-muted mb-2">
        <span class="{{ $c->percentage >= 100 ? 'text-success fw-700' : 'text-muted' }}">
        {{ $c->formatted_raised }}
    </span>
    <span class="{{ $c->percentage >= 100 ? 'text-success fw-700' : 'text-muted' }}">
        {{ $c->percentage }}%
        @if($c->percentage >= 100) @endif
    </span>
    </div>
    @endif

    @if($c->status==='rejected' && $c->rejection_reason)
    <div class="alert alert-danger py-2 px-2 small mb-2">
        <i class="bi bi-exclamation-circle me-1"></i>
        <strong>Ditolak:</strong> {{ Str::limit($c->rejection_reason, 80) }}
    </div>
    @endif

    <div class="d-flex gap-2 mt-auto pt-2">
        @if($c->status==='active')
        <a href="{{ route('campaigns.show',$c->slug) }}"
           class="btn btn-outline-primary btn-sm flex-grow-1">
            <i class="bi bi-eye me-1"></i>Lihat
        </a>
        @endif
        @if($c->status==='rejected')
        <a href="{{ route('campaigns.edit',$c->slug) }}"
           class="btn btn-warning btn-sm flex-grow-1 fw-700">
            <i class="bi bi-pencil me-1"></i>Edit & Kirim Ulang
        </a>
        @endif
        {{-- <form method="POST" action="{{ route('campaigns.destroy',$c->slug) }}"
              onsubmit="return confirm('Hapus campaign ini?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash"></i>
            </button>
        </form> --}}
    </div>
</div>
</div>
@endforeach
</div>
@else
<div class="text-center py-5 text-muted">
    <i class="bi bi-clipboard fs-1 d-block mb-2"></i>
    <p class="small">Tidak ada campaign di kategori ini</p>
</div>
@endif
