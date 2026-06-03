<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundUsage extends Model
{
    protected $fillable = [
        'campaign_id', 'title', 'description',
        'budget', 'used', 'usage_date', 'status',
    ];

    protected $casts = [
        'budget'     => 'decimal:2',
        'used'       => 'decimal:2',
        'usage_date' => 'date',
    ];

    public function campaign() { return $this->belongsTo(Campaign::class); }
    public function proofs()   { return $this->hasMany(FundUsageProof::class); }

    public function getPercentageAttribute(): int
    {
        if (!$this->budget || $this->budget == 0) return 0;
        return (int) min(100, ($this->used / $this->budget) * 100);
    }

    public function getFormattedBudgetAttribute(): string
    {
        return 'Rp ' . number_format($this->budget, 0, ',', '.');
    }

    public function getFormattedUsedAttribute(): string
    {
        return 'Rp ' . number_format($this->used, 0, ',', '.');
    }
}
