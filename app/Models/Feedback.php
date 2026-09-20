<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedback';
    protected $fillable = ['event_id', 'staff_id', 'rating', 'comment'];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function staff(): BelongsTo { return $this->belongsTo(Staff::class); }
}