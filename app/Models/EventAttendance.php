<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAttendance extends Model
{
    protected $table = 'event_attendance';

    protected $fillable = [
        'event_id', 'staff_id', 'attended', 'qr_checked_in',
        'registered_at', 'checked_in_at',
    ];

    protected $casts = [
        'attended'      => 'boolean',
        'qr_checked_in' => 'boolean',
        'registered_at' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function staff(): BelongsTo { return $this->belongsTo(Staff::class); }
}