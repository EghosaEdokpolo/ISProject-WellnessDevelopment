<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointsTransaction extends Model
{
    protected $fillable = ['staff_id', 'event_id', 'points', 'reason', 'academic_year'];

    public function staff(): BelongsTo { return $this->belongsTo(Staff::class); }
    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
}