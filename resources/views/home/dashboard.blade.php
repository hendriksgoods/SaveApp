@extends('layouts.app')
@section('title', 'Dashboard – SaveThem')

@section('content')
<div class="bg-primary text-white py-4">
    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4 class="fw-800 mb-0">Halo, {{ auth()->user()->name }}! 👋</h4>
            <p class="mb-0 text-white text-opacity-75 small">Kelola campaign Anda di sini</p>
        </div>
        <a href="{{ route('campaigns.create') }}" class="btn btn-accent">
            <i class="bi bi-plus-lg me-1"></i>Buat Campaign Baru
        </a>
    </div>
</div>

<div class="bg-light py-4">
<div class="container">

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 text-center">
                <div class="fs-2 mb-1">📋</div>
                <div class="fw-800 fs-4">{{ $campaigns->count() }}</div>
                <div class="text-muted small">Total Campaign</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 text-center">
                <div class="fs-2 mb-1">✅</div>
                <div class="fw-800 fs-4">{{ $campaigns->where('status','active')->count() }}</div>
                <div class="text-muted small">Campaign Aktif</div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm p-3 text-center">
                <div class="fs-2 mb-1">💰</div>
                <div class="fw-800 fs-4">Rp {{ number_format($totalRaised/1000000,1) }}Jt</div>
                <div class="text-muted small">Total Terkumpul</div>
            </div>
        </div>
    </div>

    {{-- Campaign Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-700 mb-0">Campaign Saya</h5>
        </div>
        @if($campaigns->count())
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="fw-600 small py-3">Campaign</th>
                        <th class="fw-600 small">Status</th>
                        <th class="fw-600 small">Progress</th>
                        <th class="fw-600 small">Donatur</th>
                        <th class="fw-600 small">Sisa Hari</th>
                        <th class="fw-600 small">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($campaigns as $c)
                    <tr>
                        <td style="max-width:220px;">
                            <div class="fw-600 small text-truncate">{{ $c->title }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $c->category }}</div>
                        </td>
                        <td>
                            @php
                            $statusMap = [
                                'active'    => ['success', 'Aktif'],
                                'pending'   => ['warning', 'Review'],
                                'completed' => ['primary', 'Selesai'],
                                'rejected'  => ['danger',  'Ditolak'],
                            ];
                            [$color, $label] = $statusMap[$c->status] ?? ['secondary', $c->status];
                            @endphp
                            <span class="badge bg-{{ $color }}-subtle text-{{ $color }} px-2 py-1">
                                {{ $label }}
                            </span>
                        </td>
                        <td style="min-width:140px;">
                            <div class="progress mb-1" style="height:5px;">
                                <div class="progress-bar bg-primary"
                                     style="width: {{ min($campaign->percentage, 100) }}%"></div>
                            </div>
                            <small class="text-muted">{{ $c->percentage }}% — {{ $c->formatted_raised }}</small>
                        </td>
                        <td>
                            <small>{{ $c->donations_count }}</small>
                        </td>
                        <td>
                            <small class="{{ $c->days_left <= 7 ? 'text-danger fw-600' : 'text-muted' }}">
                                {{ $c->days_left }} hari
                            </small>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('campaigns.show', $c->slug) }}"
                                   class="btn btn-outline-secondary btn-sm" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('campaigns.edit', $c->slug) }}"
                                   class="btn btn-outline-primary btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('campaigns.destroy', $c->slug) }}"
                                      onsubmit="return confirm('Hapus campaign ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div class="text-center py-5 text-muted">
                <div class="fs-1 mb-3">📋</div>
                <h5>Belum ada campaign</h5>
                <p class="small">Mulai buat campaign pertama Anda</p>
                <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Buat Campaign
                </a>
            </div>
        @endif
    </div>

</div>
</div>
@endsection
