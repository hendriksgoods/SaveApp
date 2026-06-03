<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    protected $fillable = ['campaign_id', 'user_id', 'parent_id', 'body', 'likes'];

    public function campaign() { return $this->belongsTo(Campaign::class); }
    public function user()     { return $this->belongsTo(User::class); }

    // Balasan dari penggalang dana
    public function replies()
    {
        return $this->hasMany(ForumComment::class, 'parent_id')->with('user')->latest();
    }

    // Hanya komentar utama (bukan balasan)
    public function scopeTopLevel($q)
    {
        return $q->whereNull('parent_id');
    }
}
