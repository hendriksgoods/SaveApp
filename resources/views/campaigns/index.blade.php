@extends('layouts.app')
@section('title', 'Semua Campaign – GalangDana')

@section('content')
<div class="bg-primary text-white py-4">
    <div class="container">
        <h2 class="fw-800 mb-1">Semua Campaign</h2>
        <p class="mb-0 text-white text-opacity-75 small">Temukan campaign yang ingin Anda dukung</p>
    </div>
</div>

<div class="bg-light py-4">
<div class="container">

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm p-3 mb-4">
        <form action="{{ route('campaigns.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-600 mb-1">Cari Campaign</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" name="search"
                           value="{{ $search }}" placeholder="Judul atau nama penggalang...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-600 mb-1">Kategori</label>
                <select class="form-select" name="category">
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-600 mb-1">Urutkan</label>
                <select class="form-select" name="sort">
                    <option value="newest"     {{ $sort === 'newest'     ? 'selected' : '' }}>Terbaru</option>
                    <option value="most_raised"{{ $sort === 'most_raised'? 'selected' : '' }}>Dana Terbanyak</option>
                    <option value="urgent"     {{ $sort === 'urgent'     ? 'selected' : '' }}>Mendesak</option>
                    <option value="ending_soon"{{ $sort === 'ending_soon'? 'selected' : '' }}>Segera Berakhir</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Results Count --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <p class="text-muted small mb-0">
            Menampilkan <strong>{{ $campaigns->total() }}</strong> campaign
            @if($search) untuk "<strong>{{ $search }}</strong>" @endif
        </p>
        @auth
            @if(auth()->user()->isPenggalang())
            <a href="{{ route('campaigns.create') }}" class="btn btn-accent btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Buat Campaign
            </a>
            @endif
        @endauth
    </div>

    {{-- Campaign Grid --}}
    @if($campaigns->count())
        <div class="row g-4">
            @foreach($campaigns as $campaign)
                @include('campaigns._card', ['campaign' => $campaign])
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">
            {{ $campaigns->links() }}
        </div>
    @else
        <div class="text-center py-5 text-muted">
            <div class="fs-1 mb-3">🔍</div>
            <h5>Tidak ada campaign ditemukan</h5>
            <p class="small">Coba kata kunci atau kategori lain</p>
            <a href="{{ route('campaigns.index') }}" class="btn btn-outline-primary btn-sm">Reset Filter</a>
        </div>
    @endif

</div>
</div>
@endsection
