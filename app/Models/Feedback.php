<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedback'; // Laravel would otherwise guess "feedback" -> stays same, but explicit is clearer
    protected $fillable = ['registration_id', 'satisfaction_stars', 'social_quality', 'comments'];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
