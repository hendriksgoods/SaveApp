<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FundUsageProof extends Model
{
    protected $fillable = ['fund_usage_id', 'image_path'];

    public function fundUsage() { return $this->belongsTo(FundUsage::class); }
}
