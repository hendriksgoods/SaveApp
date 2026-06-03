<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\FundUsageController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\CampaignUpdateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\PasswordController;



// ── Public ───────────────────────────────────────────
Route::get('/', [HomeController::class,'index'])->name('home');
Route::get('/campaigns', [CampaignController::class,'index'])->name('campaigns.index');


// ── Auth ─────────────────────────────────────────────
Route::middleware('guest')->group(function(){
    Route::get('/register',  [AuthController::class,'showRegister'])->name('register');
    Route::post('/register', [AuthController::class,'register']);
    Route::get('/login',     [AuthController::class,'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class,'login']);
});
Route::post('/logout',[AuthController::class,'logout'])->name('logout')->middleware('auth');



// ── Admin ─────────────────────────────────────────────
    Route::middleware('auth')->prefix('admin')->name('admin.')->group(function(){
    Route::get('/dashboard',                        [AdminController::class,'dashboard'])->name('dashboard');
    
    
    Route::get('/verifications',                    [VerificationController::class,'index'])->name('verifications.index');
    Route::post('/verifications/{user}/approve',    [VerificationController::class,'approve'])->name('verifications.approve');
    Route::post('/verifications/{user}/approve',   [VerificationController::class,'approve'])->name('verifications.approve');
    
    Route::post('/verifications/{user}/reject',    [VerificationController::class,'reject'])->name('verifications.reject');
    Route::post('/verifications/{user}/revoke',    [VerificationController::class,'revoke'])->name('verifications.revoke');
   
    Route::get('/campaigns/{campaign}/review',      [AdminController::class,'review'])->name('campaigns.review');
    Route::post('/campaigns/{campaign}/approve',    [AdminController::class,'approve'])->name('campaigns.approve');
    Route::post('/campaigns/{campaign}/reject',     [AdminController::class,'reject'])->name('campaigns.reject');
});

    // ── Authenticated ─────────────────────────────────────
    Route::middleware('auth')->group(function(){

    // Profile
    Route::get('/profile',  [ProfileController::class,'show'])->name('profile.show');
    Route::put('/profile',  [ProfileController::class,'update'])->name('profile.update');

    // Verifikasi penggalang
    Route::get('/verification/request',  [VerificationController::class,'request'])->name('verification.request');
    Route::post('/verification/submit',  [VerificationController::class,'submit'])->name('verification.submit');

    Route::put('/profile',  [ProfileController::class,'update'])->name('profile.update');

    // Penggalang dashboard
    Route::get('/dashboard',[HomeController::class,'dashboard'])->name('fundraiser.dashboard');

    // Campaign CRUD
    Route::get('/campaigns/create',               [CampaignController::class,'create'])->name('campaigns.create');
    Route::post('/campaigns',                      [CampaignController::class,'store'])->name('campaigns.store');
    Route::get('/campaigns/{campaign:slug}/edit',  [CampaignController::class,'edit'])->name('campaigns.edit');
    Route::put('/campaigns/{campaign:slug}',       [CampaignController::class,'update'])->name('campaigns.update');
    Route::delete('/campaigns/{campaign:slug}',    [CampaignController::class,'destroy'])->name('campaigns.destroy');

    // Donasi — hanya donatur (guard di controller)
    Route::post('/campaigns/{campaign:slug}/donate',[DonationController::class,'store'])->name('donations.store');

    // Payment 
    Route::get('/payment/{transaction_id}',          [DonationController::class,'payment'])->name('payment.show');
    Route::post('/payment/{transaction_id}/confirm', [DonationController::class,'confirm'])->name('payment.confirm');
   
    // Galeri
    Route::post('/campaigns/{campaign:slug}/gallery',             [GalleryController::class,'store'])->name('gallery.store');
    Route::delete('/campaigns/{campaign:slug}/gallery/{gallery}', [GalleryController::class,'destroy'])->name('gallery.destroy');

    // Transparansi
    Route::post('/campaigns/{campaign:slug}/fund-usage',           [FundUsageController::class,'store'])->name('fund_usage.store');
    Route::delete('/campaigns/{campaign:slug}/fund-usage/{usage}', [FundUsageController::class,'destroy'])->name('fund_usage.destroy');

    // Forum
    Route::post('/campaigns/{campaign:slug}/forum',                    [ForumController::class,'store'])->name('forum.store');
    Route::post('/campaigns/{campaign:slug}/forum/{comment}/reply',    [ForumController::class,'reply'])->name('forum.reply');
    Route::post('/campaigns/{campaign:slug}/forum/{comment}/like',     [ForumController::class,'like'])->name('forum.like');
    
    //Delete komen/reply
    Route::delete('/campaigns/{campaign:slug}/forum/{comment}',              [ForumController::class,'destroy'])->name('forum.destroy');
    Route::delete('/campaigns/{campaign:slug}/forum/{comment}/reply-delete', [ForumController::class,'destroyReply'])->name('forum.destroyReply');
    
    // Jejak Kebaikan
    Route::post('/campaigns/{campaign:slug}/updates',             [CampaignUpdateController::class,'store'])->name('updates.store');
    Route::delete('/campaigns/{campaign:slug}/updates/{update}',  [CampaignUpdateController::class,'destroy'])->name('updates.destroy');

    //Penarikan Dana
    Route::get('/campaigns/{campaign:slug}/withdrawals',  [WithdrawalController::class,'index'])->name('withdrawals.index');
    Route::post('/campaigns/{campaign:slug}/withdrawals', [WithdrawalController::class,'store'])->name('withdrawals.store');
});

// ── Password Reset & Change ──────────────────────────
    Route::middleware('guest')->group(function(){
    Route::get('/forgot-password',        [\App\Http\Controllers\PasswordController::class,'showForgot'])->name('password.forgot');
    Route::post('/forgot-password',       [\App\Http\Controllers\PasswordController::class,'sendReset'])->name('password.send');
    Route::get('/reset-password',         [\App\Http\Controllers\PasswordController::class,'showReset'])->name('password.reset');
    Route::post('/reset-password',        [\App\Http\Controllers\PasswordController::class,'reset'])->name('password.reset.post');
});

    Route::middleware('auth')->group(function(){
    Route::get('/change-password',        [\App\Http\Controllers\PasswordController::class,'showChange'])->name('password.change');
    Route::post('/change-password',       [\App\Http\Controllers\PasswordController::class,'change'])->name('password.change.post');
});


    //Detail Capmpaign
    Route::get('/campaigns/{campaign:slug}', [CampaignController::class,'show'])
    ->name('campaigns.show');