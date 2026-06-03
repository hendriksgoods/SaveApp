@extends('layouts.app')
@section('title','SaveThem – Berbagi Kebaikan, Wujudkan Harapan')
@section('content')

{{-- Hero --}}
<section style="background:linear-gradient(135deg,#6B21A8 0%,#7C3AED 40%,#EC4899 100%);padding:80px 0;">
    <div class="container text-center text-white">
        <h1 class="display-4 fw-800 mb-3">Berbagi Kebaikan, Wujudkan Harapan</h1>
        <p class="lead mb-5 text-white text-opacity-85">Platform donasi transparan dan terpercaya untuk membantu sesama yang membutuhkan</p>
        <div class="row justify-content-center g-3 mb-5">
            <div class="col-md-3"><div class="stat-card-hero"><i class="bi bi-graph-up-arrow d-block fs-2 mb-2"></i><div class="fw-800 fs-3">Rp {{ number_format($stats['total_raised']/1000000,0) }}M+</div><div class="small text-white text-opacity-75">Dana Terkumpul</div></div></div>
            <div class="col-md-3"><div class="stat-card-hero"><i class="bi bi-people d-block fs-2 mb-2"></i><div class="fw-800 fs-3">{{ number_format($stats['total_donors']) }}+</div><div class="small text-white text-opacity-75">Donatur Aktif</div></div></div>
            <div class="col-md-3"><div class="stat-card-hero"><i class="bi bi-shield-check d-block fs-2 mb-2"></i><div class="fw-800 fs-3">{{ $stats['total_campaigns'] }}</div><div class="small text-white text-opacity-75">Campaign Terverifikasi</div></div></div>
        </div>
        <form action="{{ route('home') }}" method="GET" class="d-flex justify-content-center">
            <div class="search-bar">
                <i class="bi bi-search search-icon"></i>
                <input type="text" name="search" value="{{ $search }}" class="form-control search-input" placeholder="Cari campaign">
                <button type="submit" class="btn btn-white search-btn fw-700">Cari</button>
            </div>
        </form>
    </div>
</section>

{{-- Campaigns --}}
<section class="py-5 bg-light">
<div class="container">
    <div class="d-flex gap-2 flex-wrap mb-4">
        @foreach($categories as $cat)
        <a href="{{ route('home', array_merge(request()->query(),['category'=>$cat])) }}"
           class="btn btn-sm category-pill {{ $category===$cat ? 'active' : '' }}">{{ $cat }}</a>
        @endforeach
    </div>

    @if($campaigns->count())
        <div class="row g-4">
            @foreach($campaigns as $c)
                @include('campaigns._card',['campaign'=>$c])
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">{{ $campaigns->links() }}</div>
    @else
        <div class="text-center py-5 text-muted"><div class="fs-1 mb-3">🔍</div><h5>Tidak ada campaign ditemukan</h5></div>
    @endif
</div>
</section>
@endsection
