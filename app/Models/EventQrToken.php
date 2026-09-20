<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventQrToken extends Model
{
    protected $fillable = ['event_id', 'token', 'expires_at'];
    protected $casts = ['expires_at' => 'datetime'];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
}