@extends('layouts.app')
@section('title', isset($editing) ? 'Edit Campaign – SaveThem' : 'Buat Campaign Baru – SaveThem')

@section('content')
<div class="bg-light py-4" style="min-height:85vh;">
<div class="container">
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card border-0 shadow-sm">
<div class="card-body p-4">

{{-- Header --}}
<div class="d-flex justify-content-between align-items-start mb-1">
    <div>
        <h5 class="fw-800 mb-0">{{ isset($editing) ? 'Edit Campaign' : 'Buat Campaign Baru' }}</h5>
        <p class="text-muted small mb-0" id="stepLabel">Langkah 1 dari 7</p>
    </div>
    <a href="{{ route('fundraiser.dashboard') }}" class="btn btn-sm btn-light border">
        <i class="bi bi-x-lg"></i>
    </a>
</div>

{{-- Step Wizard --}}
<div class="d-flex align-items-start my-4 overflow-auto pb-1" id="stepWizard">
    @foreach([1=>'Kategori',2=>'Persetujuan',3=>'Data Pribadi',4=>'Detail Campaign',5=>'Pendanaan',6=>'Alasan',7=>'Konfirmasi'] as $n => $lbl)
        <div class="d-flex flex-column align-items-center flex-shrink-0" id="sw{{$n}}">
            <div class="step-dot" id="swd{{$n}}"><span id="swi{{$n}}">{{$n}}</span></div>
            <div class="step-lbl">{{$lbl}}</div>
        </div>
        @if($n < 7)
            <div class="step-line mt-2 flex-shrink-0" id="swl{{$n}}"></div>
        @endif
    @endforeach
</div>

<form action="{{ isset($editing) ? route('campaigns.update', $campaign->slug) : route('campaigns.store') }}"
      method="POST" enctype="multipart/form-data" id="campaignForm">
@csrf
@if(isset($editing)) @method('PUT') @endif

{{-- =================== STEP 1: KATEGORI =================== --}}
<div class="step-panel" id="panel1">
    <h6 class="fw-700 mb-1">Pilih Kategori</h6>
    <p class="text-muted small mb-3">Pilih kategori yang paling sesuai dengan campaign Anda</p>

    <div class="row g-2">
        @foreach([
            ['Pendidikan',            '📚', 'Beasiswa, biaya sekolah, pelatihan'],
            ['Kesehatan',             '🏥', 'Biaya pengobatan, operasi'],
            ['Bencana Alam',          '🆘', 'Bantuan korban bencana, darurat'],
            ['Alam & Lingkungan',     '🌱', 'Konservasi, reboisasi, pelestarian'],
            ['Kebutuhan Sehari-hari', '🏠', 'Panti asuhan, lansia, yatim piatu'],
        ] as [$cat, $emoji, $desc])
        <div class="col-6">
            <div class="cat-card {{ (isset($campaign) && $campaign->category===$cat) || old('category')===$cat ? 'selected' : '' }}"
                 data-cat="{{ $cat }}" onclick="selectCat(this)">
                <span style="font-size:28px;display:block;margin-bottom:6px;">{{ $emoji }}</span>
                <div class="fw-700 small">{{ $cat }}</div>
                <div class="text-muted" style="font-size:11px;">{{ $desc }}</div>
            </div>
        </div>
        @endforeach
    </div>
    <input type="hidden" name="category" id="catInput"
           value="{{ isset($campaign) ? $campaign->category : old('category') }}">
    <div class="text-danger small mt-2 d-none" id="catErr">Pilih kategori terlebih dahulu.</div>

    <div class="d-flex justify-content-end mt-4">
      <button type="button" class="btn btn-primary px-4 fw-700" onclick="step1Next()">
    Lanjut ke Persetujuan <i class="bi bi-arrow-right ms-1"></i>
      </button>
    </div>
</div>

{{-- =================== STEP 2: PERSETUJUAN =================== --}}
<div class="step-panel d-none" id="panel2">
    <h5 class="fw-700 mb-1">Persetujuan Pengisian Data Pribadi</h5>
    <p class="text-muted small mb-3">Untuk membangun kepercayaan dengan para donatur, kami memerlukan data pribadi Anda</p>

    <div class="alert alert-primary border-0 d-flex gap-2 mb-3">
        <i class="bi bi-shield-check text-primary mt-1 flex-shrink-0 fs-5"></i>
        <div class="small">
            <strong>Mengapa kami meminta data pribadi?</strong><br>
            Data pribadi yang Anda berikan akan digunakan untuk proses verifikasi dan membangun kepercayaan dengan para donatur. Transparansi adalah kunci kesuksesan campaign Anda.
        </div>
    </div>

    <div class="card bg-light border-0 p-3 mb-3">
        <p class="fw-700 small mb-3">Data yang akan kami minta:</p>
        @foreach([
            ['Nama Lengkap Sesuai KTP','Untuk verifikasi identitas Anda'],
            ['Kartu Tanda Penduduk (KTP)','Nomor KTP 16 digit untuk verifikasi'],
            ['Nomor Telepon','Untuk komunikasi dan verifikasi'],
            ['Pekerjaan Saat Ini','Untuk memverifikasi kredibilitas Anda'],
            ['Akun Media Sosial','Untuk meningkatkan kepercayaan donatur'],
        ] as [$title, $desc])
        <div class="d-flex align-items-start gap-2 mb-2">
            <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
            <div class="small"><strong>{{ $title }}</strong><br>
            <span class="text-muted">{{ $desc }}</span></div>
        </div>
        @endforeach
    </div>

    <div class="alert alert-warning border-0 small mb-3">
        <strong>Catatan:</strong> Semua data pribadi Anda akan dijaga kerahasiaannya dan hanya digunakan untuk proses verifikasi. Data tidak akan dibagikan kepada pihak ketiga tanpa izin Anda.
    </div>

    <div class="form-check mb-4">
        <input type="checkbox" class="form-check-input" id="agreeData"
               {{ isset($campaign) ? 'checked' : '' }}>
        <label class="form-check-label small" for="agreeData">
            Saya memahami dan menyetujui untuk memberikan data pribadi saya untuk proses verifikasi campaign. Saya juga telah membaca dan menyetujui
            <a href="#" class="text-primary fw-600">Syarat & Ketentuan</a> serta
            <a href="#" class="text-primary fw-600">Kebijakan Privasi</a> platform SaveThem.
        </label>
        <div class="text-danger small d-none mt-1" id="agreeDataErr">Anda harus menyetujui terlebih dahulu.</div>
    </div>

    <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-outline-secondary px-4" onclick="goTo(1)">Kembali</button>
        <button type="button" class="btn btn-primary px-4 fw-700" onclick="checkAgree()">
            Saya Setuju & Lanjutkan <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </div>
</div>

{{-- =================== STEP 3: DATA PRIBADI =================== --}}
<div class="step-panel d-none" id="panel3">
    <h5 class="fw-700 mb-1">Data Pribadi Penggalang Dana</h5>
    <p class="text-muted small mb-3">Lengkapi data diri Anda untuk proses verifikasi campaign</p>

    {{-- Nama Lengkap sesuai KTP --}}
    <div class="mb-3">
        <label class="form-label fw-600 small">Nama Lengkap Sesuai KTP <span class="text-danger">*</span></label>
        <input type="text" name="full_name_ktp" id="fullNameKtp"
               class="form-control @error('full_name_ktp') is-invalid @enderror"
               value="{{ isset($campaign) ? $campaign->full_name_ktp : old('full_name_ktp', auth()->user()->name) }}"
               placeholder="Nama sesuai KTP (hanya huruf)">
        <div class="form-text">Hanya boleh huruf dan spasi, tidak boleh mengandung angka</div>
        <div class="text-danger small d-none mt-1" id="fullNameErr">Nama hanya boleh berisi huruf dan spasi.</div>
        @error('full_name_ktp')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Nomor KTP --}}
    <div class="mb-3">
        <label class="form-label fw-600 small">Nomor KTP <span class="text-danger">*</span></label>
        <input type="text" name="ktp_number" id="ktpNumber"
               class="form-control @error('ktp_number') is-invalid @enderror"
               value="{{ isset($campaign) ? $campaign->ktp_number : old('ktp_number') }}"
               placeholder="16 digit nomor KTP" maxlength="16" inputmode="numeric">
        <div class="text-danger small d-none mt-1" id="ktpErr">Nomor KTP harus tepat 16 angka.</div>
        @error('ktp_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Nomor Telepon --}}
    <div class="mb-3">
        <label class="form-label fw-600 small">Nomor Telepon <span class="text-danger">*</span></label>
        <input type="text" name="phone" id="step3phone"
               class="form-control @error('phone') is-invalid @enderror"
               value="{{ isset($campaign) ? $campaign->phone : old('phone', auth()->user()->phone) }}"
               placeholder="12 digit (contoh: 081234567890)" maxlength="12" inputmode="numeric">
        <div class="text-danger small d-none mt-1" id="phoneStep3Err">Nomor telepon harus tepat 12 angka.</div>
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Pekerjaan --}}
    <div class="mb-3">
        <label class="form-label fw-600 small">Pekerjaan Saat Ini <span class="text-danger">*</span></label>
        <input type="text" name="occupation" id="occupation"
               class="form-control @error('occupation') is-invalid @enderror"
               value="{{ isset($campaign) ? $campaign->occupation : old('occupation') }}"
               placeholder="Contoh: Guru, Wiraswasta, Karyawan Swasta">
        <div class="text-danger small d-none mt-1" id="occErr">Pekerjaan wajib diisi.</div>
        @error('occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Medsos --}}
    <p class="fw-700 small mb-2 mt-3">Akun Media Sosial
        <span class="text-muted fw-400">(Opsional)</span>
    </p>
    @foreach([
        ['facebook',  'Facebook',  'https://facebook.com/username'],
        ['instagram', 'Instagram', 'https://instagram.com/username'],
        ['twitter',   'Twitter/X', 'https://twitter.com/username'],
    ] as [$field, $label, $ph])
    <div class="mb-3">
        <label class="form-label fw-600 small">{{ $label }}</label>
        <input type="url" name="{{ $field }}"
               class="form-control @error($field) is-invalid @enderror"
               value="{{ isset($campaign) ? $campaign->$field : old($field) }}"
               placeholder="{{ $ph }}">
        @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @endforeach

    <div class="d-flex justify-content-between mt-3">
        <button type="button" class="btn btn-outline-secondary px-4" onclick="goTo(2)">Kembali</button>
        <button type="button" class="btn btn-primary px-4 fw-700" onclick="validateStep3()">
            Lanjutkan <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </div>
</div>

{{-- =================== STEP 4: DETAIL CAMPAIGN =================== --}}
<div class="step-panel d-none" id="panel4">
    <h5 class="fw-700 mb-1">Detail Campaign</h5>
    <p class="text-muted small mb-3">Ceritakan detail campaign Anda dengan jelas dan menarik</p>

    <div class="mb-3">
        <label class="form-label fw-600 small">Judul Campaign <span class="text-danger">*</span></label>
        <input type="text" name="title" id="titleInput" maxlength="100"
               class="form-control @error('title') is-invalid @enderror"
               value="{{ isset($campaign) ? $campaign->title : old('title') }}">
        <div class="d-flex justify-content-between">
            <div class="form-text">Minimal 10 karakter</div>
            <small class="text-muted"><span id="titleCount">0</span>/100</small>
        </div>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-600 small">Cerita Campaign <span class="text-danger">*</span></label>
        <textarea name="story" rows="4"
                  class="form-control @error('story') is-invalid @enderror"
                  placeholder="Ceritakan latar belakang campaign Anda secara detail...">{{ isset($campaign) ? $campaign->story : old('story') }}</textarea>
        <div class="d-flex justify-content-between">
            <div class="form-text">Minimal 50 karakter</div>
            <small class="text-muted" id="storyCount">0 karakter</small>
        </div>
        @error('story')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-600 small">Deskripsi Lengkap <span class="text-danger">*</span></label>
        <textarea name="description" rows="5"
                  class="form-control @error('description') is-invalid @enderror"
                  placeholder="Jelaskan secara detail kondisi saat ini, apa yang sudah dilakukan, dan mengapa bantuan sangat dibutuhkan...">{{ isset($campaign) ? $campaign->description : old('description') }}</textarea>
        <div class="d-flex justify-content-between">
            <div class="form-text">Minimal 200 karakter</div>
            <small class="text-muted" id="descCount">0 karakter</small>
        </div>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-600 small">Tujuan Penggalangan Dana <span class="text-danger">*</span></label>
        <div class="form-text mb-1">Pisahkan dengan Enter untuk setiap poin</div>
        <textarea name="fund_purpose" rows="4"
                  class="form-control @error('fund_purpose') is-invalid @enderror"
                  placeholder="Contoh:&#10;Biaya operasi jantung&#10;Perawatan ICU pasca operasi&#10;Obat-obatan dan pemeriksaan lanjutan">{{ isset($campaign) ? $campaign->fund_purpose : old('fund_purpose') }}</textarea>
        @error('fund_purpose')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-600 small">Lokasi <span class="text-danger">*</span></label>
        <input type="text" name="location"
               class="form-control @error('location') is-invalid @enderror"
               value="{{ isset($campaign) ? $campaign->location : old('location') }}"
               placeholder="Contoh: Jakarta Selatan, DKI Jakarta">
        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="d-flex justify-content-between mt-3">
        <button type="button" class="btn btn-outline-secondary px-4" onclick="goTo(3)">Kembali</button>
        <button type="button" class="btn btn-primary px-4 fw-700" onclick="validateStep4()">
            Lanjutkan <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </div>
</div>

{{-- =================== STEP 5: PENDANAAN =================== --}}
<div class="step-panel d-none" id="panel5">
    <h5 class="fw-700 mb-1">Detail Pendanaan</h5>
    <p class="text-muted small mb-3">Tentukan target dana dan durasi campaign Anda</p>

    <div class="mb-3">
        <label class="form-label fw-600 small">Target Dana <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text fw-600 bg-white">Rp</span>
            <input type="number" name="target_amount" id="targetInput"
                   class="form-control @error('target_amount') is-invalid @enderror"
                   value="{{ isset($campaign) ? (int)$campaign->target_amount : old('target_amount', 0) }}"
                   min="100000" placeholder="0">
        </div>
        <div class="form-text" id="targetPreview">Minimal Rp 1.000.000</div>
        @error('target_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-600 small">Durasi Campaign <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="number" id="durationDays" name="duration_days"
                   class="form-control @error('duration_days') is-invalid @enderror"
                   value="{{ isset($campaign) ? $campaign->duration_days : old('duration_days', 30) }}"
                   min="7" max="365">
            <span class="input-group-text bg-white">hari</span>
        </div>
        <div class="form-text">Maksimal 365 hari (1 tahun)</div>
        @error('duration_days')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-600 small">Rincian Penggunaan Dana <span class="text-danger">*</span></label>
        <div class="form-text mb-1">
            Jelaskan secara detail bagaimana dana akan digunakan. Semakin detail, semakin tinggi kepercayaan donatur.
        </div>
        <textarea name="fund_detail" rows="8"
                  class="form-control @error('fund_detail') is-invalid @enderror"
                  placeholder="Contoh rincian detail:&#10;&#10;1. Biaya Operasi Jantung - Rp 80.000.000&#10;   - Biaya dokter spesialis: Rp 25.000.000&#10;   - Biaya ruang operasi: Rp 30.000.000&#10;&#10;2. Perawatan ICU - Rp 40.000.000">{{ isset($campaign) ? $campaign->fund_detail : old('fund_detail') }}</textarea>
        <div class="d-flex justify-content-between mt-1">
            <div class="form-text">Minimal 100 karakter</div>
            <small class="text-muted" id="fundDetailCount">0 karakter</small>
        </div>
        @error('fund_detail')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="alert alert-primary border-0 small">
        <i class="bi bi-lightbulb me-1"></i>
        <strong>Tips:</strong> Rincian yang detail dan transparan akan meningkatkan kepercayaan donatur hingga 3x lipat. Sertakan estimasi biaya untuk setiap item.
    </div>

    <div class="d-flex justify-content-between mt-3">
        <button type="button" class="btn btn-outline-secondary px-4" onclick="goTo(4)">Kembali</button>
        <button type="button" class="btn btn-primary px-4 fw-700" onclick="validateStep5()">
            Lanjutkan <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </div>
</div>

{{-- =================== STEP 6: ALASAN =================== --}}
<div class="step-panel d-none" id="panel6">
    <h5 class="fw-700 mb-1">Alasan Menggalang Dana</h5>
    <p class="text-muted small mb-3">Tulis dengan tulus dan jujur mengapa campaign ini sangat penting</p>

    <div class="mb-3">
        <label class="form-label fw-600 small">Alasan Kuat Menggalang Dana <span class="text-danger">*</span></label>
        <div class="form-text mb-1">
            Ceritakan dari hati Anda mengapa campaign ini sangat penting. Donatur akan lebih tergerak dengan cerita yang meyankinkan.
        </div>
        <textarea name="reason" rows="12"
                  class="form-control @error('reason') is-invalid @enderror">{{ isset($campaign) ? $campaign->reason : old('reason') }}</textarea>
        <div class="d-flex justify-content-between mt-1">
            <div class="form-text">Minimal 300 karakter</div>
            <small class="text-muted" id="reasonCount">0 karakter</small>
        </div>
        @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="alert alert-warning border-0 small">
        <i class="bi bi-exclamation-triangle me-1"></i>
        <strong>Penting:</strong> Alasan yang tulus, jujur, dan detail akan sangat mempengaruhi keputusan donatur. Ceritakan situasi Anda dengan apa adanya, termasuk apa yang sudah Anda lakukan untuk mengatasi masalah ini.
    </div>

    <div class="d-flex justify-content-between mt-3">
        <button type="button" class="btn btn-outline-secondary px-4" onclick="goTo(5)">Kembali</button>
        <button type="button" class="btn btn-primary px-4 fw-700" onclick="validateStep6()">
            Lanjut ke Review <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </div>
</div>

{{-- =================== STEP 7: KONFIRMASI =================== --}}
<div class="step-panel d-none" id="panel7">
    <h5 class="fw-700 mb-1">Review & Konfirmasi</h5>
    <p class="text-muted small mb-3">Periksa kembali semua informasi sebelum mengirim pengajuan campaign</p>

    {{-- Summary cards --}}
    <div class="border rounded-3 p-3 mb-3">
        <p class="fw-700 small text-muted text-uppercase mb-2">Kategori</p>
        <div class="fw-600" id="sumCat">—</div>
    </div>

    <div class="border rounded-3 p-3 mb-3">
        <p class="fw-700 small text-muted text-uppercase mb-2">Data Pribadi</p>
        <div class="row g-1 small">
            <div class="col-6 text-muted">Nama sesuai KTP:</div><div class="col-6 fw-600" id="sumName">—</div>
            <div class="col-6 text-muted">Nomor Telepon:</div><div class="col-6 fw-600" id="sumPhone">—</div>
            <div class="col-6 text-muted">Pekerjaan:</div><div class="col-6 fw-600" id="sumOcc">—</div>
            <div class="col-6 text-muted">No. KTP:</div><div class="col-6 fw-600" id="sumKtp">—</div>
        </div>
    </div>

    <div class="border rounded-3 p-3 mb-3">
        <p class="fw-700 small text-muted text-uppercase mb-2">Detail Campaign</p>
        <div class="row g-1 small">
            <div class="col-3 text-muted">Judul:</div><div class="col-9 fw-600" id="sumTitle">—</div>
            <div class="col-3 text-muted">Lokasi:</div><div class="col-9 fw-600" id="sumLoc">—</div>
        </div>
    </div>

    <div class="border rounded-3 p-3 mb-3">
        <p class="fw-700 small text-muted text-uppercase mb-2">Target Pendanaan</p>
        <div class="d-flex justify-content-between small">
            <span class="text-muted">Target Dana:</span>
            <span class="fw-700 text-primary" id="sumTarget">—</span>
        </div>
        <div class="d-flex justify-content-between small mt-1">
            <span class="text-muted">Durasi:</span>
            <span class="fw-700" id="sumDuration">—</span>
        </div>
    </div>

    {{-- Verifikasi info --}}
    <div class="alert alert-primary border-0 small mb-3">
        <div class="d-flex gap-2">
            <i class="bi bi-info-circle-fill text-primary flex-shrink-0 mt-1"></i>
            <div>
                <strong>Proses Verifikasi</strong><br>
                Setelah mengirimkan pengajuan, tim kami akan memverifikasi:
                <ul class="mb-1 mt-1 ps-3">
                    <li>Keaslian data KTP dan identitas Anda</li>
                    <li>Kelengkapan dan kebenaran informasi campaign</li>
                    <li>Kredibilitas dan urgensi campaign</li>
                </ul>
                Proses verifikasi biasanya memakan waktu <strong>1-3 hari kerja</strong>.
            </div>
        </div>
    </div>

    {{-- Terms --}}
    <div class="border rounded-3 p-3 mb-3">
        <p class="fw-700 small mb-2">Syarat & Ketentuan</p>
        <div class="small text-muted mb-3" style="max-height:150px;overflow-y:auto;">
            <ol class="ps-3">
                <li class="mb-1">Saya menyatakan bahwa semua informasi yang saya berikan adalah benar dan dapat dipertanggungjawabkan.</li>
                <li class="mb-1">Saya bersedia untuk transparan dalam penggunaan dana dan melaporkan secara berkala kepada donatur.</li>
                <li class="mb-1">Saya memahami bahwa campaign yang tidak sesuai dengan kebijakan platform dapat dihapus atau ditolak.</li>
                <li class="mb-1">Saya setuju bahwa platform berhak memverifikasi semua informasi yang saya berikan.</li>
                <li class="mb-1">Saya bertanggung jawab penuh atas keabsahan dokumen yang saya berikan.</li>
                <li class="mb-1">Platform berhak menahan dana jika ditemukan indikasi penipuan atau pelanggaran.</li>
                <li class="mb-1">Saya bersedia menerima konsekuensi hukum jika terbukti melakukan penipuan.</li>
            </ol>
        </div>
        <div class="form-check">
            <input type="checkbox" 
            class="form-check-input" id="agreeFinal" 
            onchange="toggleSubmitBtn(this)">
            <label class="form-check-label small fw-600" for="agreeFinal">
                Saya telah membaca dan menyetujui semua <strong>Syarat & Ketentuan</strong> di atas.
            </label>
        </div>
    </div>

    <div class="alert alert-success border-0 d-flex gap-2 align-items-start small mb-3">
        <i class="bi bi-check-circle-fill text-success flex-shrink-0 mt-1"></i>
        <div>
            <strong>Siap Mengirimkan Pengajuan?</strong><br>
            Pastikan semua informasi sudah benar. Setelah dikirim, Anda masih bisa mengedit campaign jika ditolak oleh admin.
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <button type="button" class="btn btn-outline-secondary px-4" onclick="goTo(6)">Kembali</button>
        <button type="submit" class="btn btn-success px-4 fw-700" id="submitBtn" disabled>
            <i class="bi bi-send me-1"></i>Kirim Pengajuan Campaign
        </button>
    </div>
</div>

</form>
</div>
</div>
</div>
</div>
</div>
@endsection

@push('styles')
<style>
.step-dot  { width:32px;height:32px;border-radius:50%;background:#e5e7eb;color:#9ca3af;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;transition:all .2s; }
.step-dot.active { background:#1B6CA8;color:#fff; }
.step-dot.done   { background:#16a34a;color:#fff; }
.step-lbl  { font-size:10px;color:#9ca3af;margin-top:4px;text-align:center;white-space:nowrap; }
.step-line { width:28px;height:2px;background:#e5e7eb;transition:background .2s; }
.step-line.done { background:#16a34a; }
.cat-card  { border:2px solid #e5e7eb;border-radius:12px;padding:16px 10px;cursor:pointer;text-align:center;transition:all .15s; }
.cat-card:hover,.cat-card.selected { border-color:#1B6CA8;background:#EBF5FF; }
</style>
@endpush

@push('scripts')
<script>
// ---- Wizard navigation ----
function goTo(n) {
    document.querySelectorAll('.step-panel').forEach(p => p.classList.add('d-none'));
    document.getElementById('panel' + n).classList.remove('d-none');
    document.getElementById('stepLabel').textContent = 'Langkah ' + n + ' dari 7';
    for (let i = 1; i <= 7; i++) {
        const dot = document.getElementById('swd' + i);
        const ico = document.getElementById('swi' + i);
        const ln  = document.getElementById('swl' + i);
        dot.classList.remove('active','done');
        if      (i < n)  { dot.classList.add('done'); ico.innerHTML = '<i class="bi bi-check-lg"></i>'; }
        else if (i === n){ dot.classList.add('active'); ico.textContent = i; }
        else             { ico.textContent = i; }
        if (ln) ln.classList.toggle('done', i < n);
    }
    if (n === 7) fillSummary();
    window.scrollTo({top:0, behavior:'smooth'});
}

// ---- Category ----
function selectCat(el) {
    document.querySelectorAll('.cat-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('catInput').value = el.dataset.cat;
    document.getElementById('catErr').classList.add('d-none');
}

// ---- Step 1 ----
function step1Next() {
    if (!document.getElementById('catInput').value) {
        document.getElementById('catErr').classList.remove('d-none'); return;
    }
    goTo(2);
}

// ---- Step 2 ----
function checkAgree() {
    const cb = document.getElementById('agreeData');
    const err = document.getElementById('agreeDataErr');
    if (!cb.checked) { err.classList.remove('d-none'); return; }
    err.classList.add('d-none');
    goTo(3);
}

// ---- Step 3 validation ----
function validateStep3() {
    let ok = true;

    const name = document.getElementById('fullNameKtp');
    const nameErr = document.getElementById('fullNameErr');
    if (!name.value.trim() || /[0-9]/.test(name.value)) {
        name.classList.add('is-invalid'); nameErr.classList.remove('d-none'); ok = false;
    } else { name.classList.remove('is-invalid'); nameErr.classList.add('d-none'); }

    const ktp = document.getElementById('ktpNumber');
    const ktpErr = document.getElementById('ktpErr');
    if (!/^\d{16}$/.test(ktp.value)) {
        ktp.classList.add('is-invalid'); ktpErr.classList.remove('d-none'); ok = false;
    } else { ktp.classList.remove('is-invalid'); ktpErr.classList.add('d-none'); }

    const phone = document.getElementById('step3phone');
    const phoneErr = document.getElementById('phoneStep3Err');
    if (!/^\d{12}$/.test(phone.value)) {
        phone.classList.add('is-invalid'); phoneErr.classList.remove('d-none'); ok = false;
    } else { phone.classList.remove('is-invalid'); phoneErr.classList.add('d-none'); }

    const occ = document.getElementById('occupation');
    const occErr = document.getElementById('occErr');
    if (!occ.value.trim()) {
        occ.classList.add('is-invalid'); occErr.classList.remove('d-none'); ok = false;
    } else { occ.classList.remove('is-invalid'); occErr.classList.add('d-none'); }

    if (ok) goTo(4);
}

// ---- Step 4 validation ----
function validateStep4() {
    let ok = true;
    const checks = [
        [document.getElementById('titleInput'), v => v.trim().length >= 10, 'Judul minimal 10 karakter.'],
        [document.querySelector('[name=story]'), v => v.trim().length >= 50, 'Cerita minimal 50 karakter.'],
        [document.querySelector('[name=description]'), v => v.trim().length >= 200, 'Deskripsi minimal 200 karakter.'],
        [document.querySelector('[name=fund_purpose]'), v => v.trim().length > 0, 'Tujuan penggalangan wajib diisi.'],
        [document.querySelector('[name=location]'), v => v.trim().length > 0, 'Lokasi wajib diisi.'],
    ];
    checks.forEach(([el, fn, msg]) => {
        if (!fn(el.value)) { el.classList.add('is-invalid'); ok = false; }
        else el.classList.remove('is-invalid');
    });
    if (ok) goTo(5);
}

// ---- Step 5 validation ----
function validateStep5() {
    let ok = true;
    const target = document.getElementById('targetInput');
    const detail = document.querySelector('[name=fund_detail]');
    if (!target.value || parseInt(target.value) < 100000) { target.classList.add('is-invalid'); ok = false; }
    else target.classList.remove('is-invalid');
    if (detail.value.trim().length < 100) { detail.classList.add('is-invalid'); ok = false; }
    else detail.classList.remove('is-invalid');
    if (ok) goTo(6);
}

// ---- Step 6 validation ----
function validateStep6() {
    const reason = document.querySelector('[name=reason]');
    if (reason.value.trim().length < 300) { reason.classList.add('is-invalid'); return; }
    reason.classList.remove('is-invalid');
    goTo(7);
}

// ---- Summary ----
function fillSummary() {
    const rp = v => v ? 'Rp ' + parseInt(v).toLocaleString('id-ID') : '—';
    document.getElementById('sumCat').textContent      = document.getElementById('catInput').value || '—';
    document.getElementById('sumName').textContent     = document.getElementById('fullNameKtp').value || '—';
    document.getElementById('sumPhone').textContent    = document.getElementById('step3phone').value || '—';
    document.getElementById('sumOcc').textContent      = document.getElementById('occupation').value || '—';
    document.getElementById('sumKtp').textContent      = document.getElementById('ktpNumber').value || '—';
    document.getElementById('sumTitle').textContent    = document.getElementById('titleInput').value || '—';
    document.getElementById('sumLoc').textContent      = (document.querySelector('[name=location]')||{}).value || '—';
    document.getElementById('sumTarget').textContent   = rp((document.querySelector('[name=target_amount]')||{}).value);
    document.getElementById('sumDuration').textContent = ((document.querySelector('[name=duration_days]')||{}).value||'—') + ' hari';
}

// ---- Final agree ----
document.addEventListener('DOMContentLoaded', function () {

    const agreeFinal = document.getElementById('agreeFinal');
    const submitBtn  = document.getElementById('submitBtn');

    if (agreeFinal && submitBtn) {
        agreeFinal.addEventListener('change', function () {
            submitBtn.disabled = !this.checked;
        });
    }
});

// ---- Input guards ----
// KTP: only digits
document.getElementById('ktpNumber').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
// Phone step 3: only digits
document.getElementById('step3phone').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});
// Full name KTP: only letters and spaces
document.getElementById('fullNameKtp').addEventListener('input', function () {
    this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
});

// ---- Character counters ----
function counter(selector, spanId) {
    const el = typeof selector === 'string'
        ? (document.querySelector('[name="'+selector+'"]') || document.getElementById(selector))
        : selector;
    const sp = document.getElementById(spanId);
    if (!el || !sp) return;
    const update = () => sp.textContent = el.value.length + ' karakter';
    el.addEventListener('input', update);
    update();
}
document.addEventListener('DOMContentLoaded', function () {
    counter('story',       'storyCount');
    counter('description', 'descCount');
    counter('fund_detail', 'fundDetailCount');
    counter('reason',      'reasonCount');

    // Title count
    const ti = document.getElementById('titleInput');
    const tc = document.getElementById('titleCount');
    if (ti && tc) {
        ti.addEventListener('input', () => tc.textContent = ti.value.length);
        tc.textContent = ti.value.length;
    }

    // Target preview
    const tgt = document.getElementById('targetInput');
    const prev = document.getElementById('targetPreview');
    if (tgt && prev) {
        tgt.addEventListener('input', function () {
            const v = parseInt(this.value);
            if (v >= 100000) {
                prev.textContent = '= Rp ' + v.toLocaleString('id-ID');
                prev.className = 'form-text text-success fw-600';
            } else {
                prev.textContent = 'Minimal Rp 100.000';
                prev.className = 'form-text text-muted';
            }
        });
    }

    // Pre-select category on edit
    const catVal = document.getElementById('catInput').value;
    if (catVal) {
        document.querySelectorAll('.cat-card').forEach(c => {
            if (c.dataset.cat === catVal) c.classList.add('selected');
        });
    }
});
</script>
@endpush
