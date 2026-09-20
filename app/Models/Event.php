<?php

namespace App\Models;

use App\Enums\WellnessCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'description', 'wellness_category', 'starts_at', 'ends_at',
        'venue', 'capacity', 'points_awarded', 'is_published',
        'published_at', 'created_by',
    ];

    protected $casts = [
        'wellness_category' => WellnessCategory::class,
        'starts_at'         => 'datetime',
        'ends_at'           => 'datetime',
        'published_at'      => 'datetime',
        'is_published'      => 'boolean',
    ];

    public function creator(): BelongsTo { return $this->belongsTo(Staff::class, 'created_by'); }
    public function attendance(): HasMany { return $this->hasMany(EventAttendance::class); }
    public function assessments(): HasMany { return $this->hasMany(WellnessAssessment::class); }
    public function feedback(): HasMany { return $this->hasMany(Feedback::class); }
    public function qrTokens(): HasMany { return $this->hasMany(EventQrToken::class); }
    public function communications(): HasMany { return $this->hasMany(Communication::class); }
}