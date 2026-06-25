<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = [
        'campaign_id','user_id','amount',
        'bank_name','account_number','account_name',
        'description','status','processed_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function campaign() { return $this->belongsTo(Campaign::class); }
    public function user()     { return $this->belongsTo(User::class); }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'processed' => 'Berhasil Dicairkan',  
            default     => $this->status,
        };
    }
}
  

