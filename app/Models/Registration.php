<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    protected $fillable = ['user_id', 'event_id', 'status'];

    // POINTER: Registration is the "hub" model in this app — almost every
    // other table (waiver, check-in, wellbeing responses, feedback) points
    // back to a registration rather than directly to a user or event. This
    // mirrors your prototype's UC flow: you register FIRST, then everything
    // else (waiver, check-in, feedback) happens against that one registration.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function waiver(): HasOne
    {
        return $this->hasOne(Waiver::class);
    }

    public function checkIn(): HasOne
    {
        return $this->hasOne(CheckIn::class);
    }

    public function wellbeingResponses(): HasMany
    {
        return $this->hasMany(WellbeingResponse::class);
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(Feedback::class);
    }
}
