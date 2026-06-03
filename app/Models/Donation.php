<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'user_id',       // nullable (anonymous donations allowed)
        'donor_name',
        'donor_email',
        'amount',
        'message',
        'is_anonymous',
        'payment_status', // 'pending', 'paid', 'failed'
        'transaction_id',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'is_anonymous' => 'boolean',
    ];

    // Donation belongs to one campaign
    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    // Donation optionally belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor: formatted amount
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // Accessor: display name (anonymous or real)
    public function getDisplayNameAttribute(): string
    {
        return $this->is_anonymous ? 'Donatur Anonim' : $this->donor_name;
    }
}
