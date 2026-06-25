<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\JejakKebaikan;
use Illuminate\Support\Str;
class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','title','slug','category',
        'story','description','fund_purpose','location',
        'target_amount','raised_amount','duration_days','deadline',
        'fund_detail',
        'full_name_ktp','ktp_number','phone','occupation',
        'facebook','instagram','twitter',
        'reason',
        'image','status','rejection_reason','is_urgent',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'raised_amount' => 'decimal:2',
        'deadline'      => 'date',
        'is_urgent'     => 'boolean',
    ];
    public function getRouteKeyName() { return 'slug';}
    public function user()          { return $this->belongsTo(User::class); }
    public function donations()     { return $this->hasMany(Donation::class); }
    public function galleries()     { return $this->hasMany(CampaignGallery::class); }
    public function fundUsages()    { return $this->hasMany(FundUsage::class); }
    public function comments()      { return $this->hasMany(ForumComment::class); }
    public function updates()       { return $this->hasMany(JejakKebaikan::class)->latest(); }
    public function withdrawals() { return $this->hasMany(Withdrawal::class)->latest(); }
    public function getPercentageAttribute(): int
    {
        if (!$this->target_amount) return 0;
        return (int) round(($this->raised_amount / $this->target_amount) * 100);
    }

    public function getIsUrgentAttribute(): bool
    {
    return $this->raised_amount < $this->target_amount
        && $this->days_left < 10
        && $this->days_left > 0;
    }
    public function getDaysLeftAttribute(): int
    {
        return max(0, now()->diffInDays($this->deadline, false));
    }
    public function getFormattedRaisedAttribute(): string
    {
        return 'Rp ' . number_format($this->raised_amount, 0, ',', '.');
    }
    public function getFormattedTargetAttribute(): string
    {
        return 'Rp ' . number_format($this->target_amount, 0, ',', '.');
    }

    public function scopeActive($q)        { return $q->where('status','active'); }
    public function scopePending($q)       { return $q->where('status','pending'); }
    public function scopeByCategory($q,$c) { return ($c&&$c!=='Semua')?$q->where('category',$c):$q; }
    public function scopeSearch($q,$s)
    {
        if($s){$q->where(function($q2)use($s){
            $q2->where('title','like',"%$s%")
               ->orWhereHas('user',fn($u)=>$u->where('name','like',"%$s%"));
        });}
        return $q;
    }
    public static function generateSlug(string $title): string
    {
    $slug = Str::slug($title);
     $count = static::query()
        ->where('slug', 'like', $slug . '%')
        ->count();
    return $count ? "$slug-$count" : $slug;
    }
}
