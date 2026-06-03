@extends('layouts.app')

@section('title', 'Memproses Donasi')

@section('content')
<div class="bg-light py-5" style="min-height:85vh;">
<div class="container">
<div class="row justify-content-center">
<div class="col-md-6 col-lg-5">

    <div class="card border-0 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="bg-success text-white text-center py-4">
            <div class="mb-2">
                <i class="bi bi-check-circle-fill" style="font-size:56px;"></i>
            </div>

            <h4 class="fw-800 mb-1">
                Terima Kasih 🙏
            </h4>

            <p class="mb-0 small opacity-75">
                Donasi berhasil dicatat
            </p>
        </div>

        {{-- Body --}}
        <div class="card-body p-4">

            <div class="text-center mb-4">
                <h5 class="fw-700 mb-2">
                    {{ $donation->is_anonymous ? 'Donatur Baik' : $donation->donor_name }}
                </h5>

                <div class="text-muted small mb-2">
                    telah berdonasi untuk campaign
                </div>

                <div class="fw-700 text-primary">
                    {{ $campaign->title }}
                </div>
            </div>

            {{-- Nominal --}}
            <div class="bg-light rounded-3 p-3 text-center mb-4">
                <div class="small text-muted mb-1">
                    Total Donasi
                </div>

                <div class="fw-800 text-success" style="font-size:28px;">
                    {{ $donation->formatted_amount }}
                </div>
            </div>

            {{-- Pesan semangat --}}
            @if($donation->message)
            <div class="border rounded-3 p-3 mb-4">
                <div class="fw-700 small mb-2 text-muted">
                    Pesan Semangat 💌
                </div>

                <div class="fst-italic small">
                    "{{ $donation->message }}"
                </div>
            </div>
            @endif

            {{-- Countdown --}}
            <div class="alert alert-primary border-0 text-center small mb-4">
                <i class="bi bi-info-circle me-1"></i>

                Anda akan diarahkan ke halaman pembayaran dalam
                <strong id="countdown">3</strong>
                detik...
            </div>

            {{-- Button --}}
            <a href="{{ $paymentUrl }}"
               class="btn btn-primary w-100 fw-700 py-2">
                Lanjut ke Pembayaran
                <i class="bi bi-arrow-right ms-1"></i>
            </a>

        </div>
    </div>

</div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    let seconds = 3;

    const countdown = document.getElementById('countdown');

    const timer = setInterval(() => {

        seconds--;

        countdown.textContent = seconds;

        if (seconds <= 0) {

            clearInterval(timer);

            window.location.href = "{{ $paymentUrl }}";
        }

    }, 1000);

});
</script>
@endpush