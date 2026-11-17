<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'starts_at',
        'location',
        'capacity',
        'description',
        'comlink',
        'points_checkin',
        'points_feedback',
        'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pointsTransactions(): HasManyThrough
    {
        return $this->hasManyThrough(
            PointsTransaction::class,
            Registration::class,
            'event_id',        // FK on registrations
            'registration_id', // FK on points_transactions
            'id',              // PK on events
            'id'               // PK on registrations
        );
    }

    public function getSpotsRemainingAttribute(): int
    {
        $registered = $this->registrations_count ?? $this->registrations()->count();
        return max(0, $this->capacity - $registered);
    }

    public function getCategoryColorAttribute(): string
    {
        return match (strtolower($this->category ?? '')) {
            'physical'      => 'bg-blue-100 text-blue-700',
            'mental'        => 'bg-purple-100 text-purple-700',
            'financial'     => 'bg-emerald-100 text-emerald-700',
            'social'        => 'bg-amber-100 text-amber-700',
            'occupational'  => 'bg-slate-100 text-slate-700',
            'intellectual'  => 'bg-indigo-100 text-indigo-700',
            'spiritual'     => 'bg-rose-100 text-rose-700',
            'environmental' => 'bg-teal-100 text-teal-700',
            default         => 'bg-gray-100 text-gray-700',
        };
    }
}