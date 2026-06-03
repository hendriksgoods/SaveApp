<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','SaveThem – Platform Donasi Terpercaya')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <div class="brand-icon"><i class="bi bi-heart-fill text-white"></i></div>
            <div>
                <div class="brand-name">SaveThem</div>
                <div class="brand-sub">Platform Donasi Terpercaya</div>
            </div>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto ms-4 gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active fw-600':'' }}"
                       href="{{ route('home') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('campaigns.*') ? 'active fw-600':'' }}"
                       href="{{ route('campaigns.index') }}">Campaign</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                @auth
                    {{-- Buat Campaign — hanya penggalang --}}
                    @if(auth()->user()->isPenggalang())
                    <a href="{{ route('campaigns.create') }}"
                       class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1 fw-600">
                        <i class="bi bi-plus-lg"></i> Buat Campaign
                    </a>
                    @endif

                    {{-- Admin Panel --}}
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                       class="btn btn-sm btn-purple d-flex align-items-center gap-1 fw-600">
                        <i class="bi bi-shield-check"></i> Admin Panel
                    </a>
                    @endif

                    {{-- User dropdown --}}
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border d-flex align-items-center gap-2"
                                data-bs-toggle="dropdown">
                            <div class="avatar-xs">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
                            <div class="text-start d-none d-md-block" style="line-height:1.1;">
                                <div class="small fw-600">{{ auth()->user()->name }}</div>
                                <div style="font-size:10px;" class="text-muted">
                                    {{ match(auth()->user()->role){
                                        'admin'      => 'Admin',
                                        'penggalang' => 'Penggalang',
                                        default      => 'Donatur'
                                    } }}
                                </div>
                            </div>
                            <i class="bi bi-chevron-down small text-muted"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width:180px;">
                            {{-- Profile --}}
                            @if(!auth()->user()->isAdmin())
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2"
                                   href="{{ route('profile.show') }}">
                                    <i class="bi bi-person-circle text-muted"></i>Profil Saya
                                </a>
                            </li>
                            @endif

                            {{-- Dashboard penggalang --}}
                            @if(auth()->user()->isPenggalang())
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2"
                                   href="{{ route('fundraiser.dashboard') }}">
                                    <i class="bi bi-speedometer2 text-muted"></i>Dashboard
                                </a>
                            </li>
                            @endif

                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                        <i class="bi bi-box-arrow-right"></i>Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary fw-600">Daftar</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Flash messages --}}
@foreach(['success'=>'success','error'=>'danger'] as $key=>$cls)
@if(session($key))
<div class="alert alert-{{ $cls }} alert-dismissible m-0 rounded-0 border-0 border-start border-4 border-{{ $cls }} py-2">
    <div class="container small">
        <i class="bi bi-{{ $cls==='success'?'check-circle-fill text-success':'x-circle-fill text-danger' }} me-2"></i>
        {{ session($key) }}
        <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif
@endforeach

<main>@yield('content')</main>

<footer class="bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="d-flex align-items-center gap-2 mb-2">
            <div class="brand-icon"><i class="bi bi-heart-fill text-white"></i></div>
            <span class="fw-800 text-white">SaveThem</span>
        </div>
        <p class="text-secondary small">Platform donasi transparan dan terpercaya untuk membantu sesama.</p>
        <hr class="border-secondary">
        <p class="text-secondary small mb-0">© {{ date('Y') }} SaveThem. Dibuat dengan ❤️ untuk Indonesia.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
