<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

     protected $fillable = [
        'name','email','phone','password','role',
        'is_verified','verified_at','verification_note',
        'verify_full_name','verify_ktp_number',
        'verification_status','rejection_reason',
    ];

  

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'verified_at'       => 'datetime',
        'password'          => 'hashed',
        'is_verified'       => 'boolean',
    ];

    // role: 'donatur' | 'penggalang' | 'admin'
    public function isAdmin(): bool      { return $this->role === 'admin'; }
    public function isPenggalang(): bool { return $this->role === 'penggalang'; }
    public function isDonatur(): bool    { return $this->role === 'donatur'; }

    // Penggalang yang sudah diverifikasi admin
    public function isVerifiedPenggalang(): bool
    {
        return $this->isPenggalang() && $this->is_verified;
    }

        // Status label verifikasi
    public function getVerificationLabelAttribute(): string
    {
        return match($this->verification_status) {
            'pending'  => 'Menunggu Review',
            'approved' => 'Terverifikasi',
            'rejected' => 'Ditolak',
            default    => 'Belum Mengajukan',
        };
    }

    public function campaigns()  { return $this->hasMany(Campaign::class); }
    public function donations()  { return $this->hasMany(Donation::class); }
}
