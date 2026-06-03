<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CampaignUpdate extends Model
{
    protected $fillable = ['campaign_id','title','description','image_path','update_date'];
    protected $casts = ['update_date' => 'date'];
    public function campaign() { return $this->belongsTo(Campaign::class); }
}
