<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignGallery extends Model
{
    protected $fillable = ['campaign_id', 'image_path'];

    public function campaign() { return $this->belongsTo(Campaign::class); }
}
