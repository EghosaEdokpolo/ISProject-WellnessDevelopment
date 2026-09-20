<?php

namespace App\Models;

use App\Enums\AssessmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WellnessAssessment extends Model
{
    protected $fillable = [
        'event_id', 'staff_id', 'assessment_type', 'who5_score', 'assessed_at',
    ];

    protected $casts = [
        'assessment_type' => AssessmentType::class,
        'assessed_at'     => 'datetime',
    ];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function staff(): BelongsTo { return $this->belongsTo(Staff::class); }
}