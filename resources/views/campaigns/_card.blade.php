{{-- resources/views/campaigns/_card.blade.php --}}
<div class="col-md-6 col-lg-4">
    <a href="{{ route('campaigns.show', $campaign->slug) }}" class="text-decoration-none">
        <div class="campaign-card card h-100 border-0 shadow-sm">

            {{-- Campaign Image / Placeholder --}}
            <div class="campaign-thumb position-relative">
                @if($campaign->image)
                    <img src="{{ asset('storage/' . $campaign->image) }}"
                         class="card-img-top campaign-img" alt="{{ $campaign->title }}">
                @else
                    <div class="campaign-placeholder d-flex align-items-center justify-content-center
                                category-bg-{{ Str::slug($campaign->category) }}">
                        <span class="category-emoji">{{ match($campaign->category) {
                            'Kesehatan'    => '🏥',
                            'Pendidikan'   => '📚',
                            'Bencana Alam' => '🏠',
                            'Sosial'       => '🌾',
                            'Lingkungan'   => '🌿',
                            default        => '❤️'
                        } }}</span>
                    </div>
                @endif

                @if($campaign->is_urgent)
                    <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-2 py-1">
                        <i class="bi bi-exclamation-circle me-1"></i>MENDESAK
                    </span>
                @endif

                <span class="badge bg-white text-dark position-absolute top-0 end-0 m-2 border">
                    {{ $campaign->category }}
                </span>
            </div>

            <div class="card-body d-flex flex-column p-3">
                {{-- Title --}}
                <h6 class="card-title fw-700 text-dark mb-2 campaign-title">{{ $campaign->title }}</h6>

                {{-- Organizer --}}
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-xs">{{ strtoupper(substr($campaign->user->name, 0, 1)) }}</div>
                    <small class="text-muted">{{ $campaign->user->name }}</small>
                </div>

                {{-- Progress --}}
                <div class="progress mb-2" style="height: 6px;">
                    <div class="progress-bar {{ $campaign->percentage >= 100 ? 'bg-success' : ($campaign->is_urgent ? 'bg-danger' : 'bg-primary') }}"
                         style="width: {{ min(100, $campaign->percentage) }}%"
                         role="progressbar"
                         aria-valuenow="{{ $campaign->percentage }}"
                         aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-end mt-auto">
                    <div>
                        <div class="fw-700 {{ $campaign->percentage >= 100 ? 'text-success' : 'text-primary' }} small">
                                           {{ $campaign->formatted_raised }}                                            
                    </div>
                    <small class="text-muted">dari {{ $campaign->formatted_target }}</small>
                </div> 
                    <div class="text-end">
                        <div class="fw-700 text-dark small">{{ $campaign->percentage }}%</div>
                        <small class="{{ $campaign->days_left <= 10 ? 'text-danger' : 'text-muted' }}">
                            {{ $campaign->days_left }} hari lagi
                        </small>
                    </div>
                </div>

                <div class="mt-2">
                    <small class="text-muted">
                        <i class="bi bi-people me-1"></i>
                        {{ number_format($campaign->donations()->where('payment_status','paid')->count()) }} donatur
                    </small>
                </div>
            </div>
        </div>
    </a>
</div>
