<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Schema;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'name',
        'description',
        'starts_at',
        'location',
        'comlink',
        'whatsapp_channel_url',
        'category',
        'capacity',
        'points_checkin',
        'points_feedback',
        'is_published',
        'published_at',
        'points_awarded',
    ];

    protected function casts(): array
    {
        return [
            'starts_at'       => 'datetime',
            'published_at'    => 'datetime',
            'is_published'    => 'boolean',
            'capacity'        => 'integer',
            'points_checkin'  => 'integer',
            'points_feedback' => 'integer',
            'points_awarded'  => 'integer',
        ];
    }

    /**
     * Always return the WhatsApp URL whether stored in `comlink` or `whatsapp_channel_url`.
     */
    public function getWhatsappChannelUrlAttribute(?string $value): ?string
    {
        $url = $value ?: ($this->attributes['comlink'] ?? null);

        return is_string($url) && trim($url) !== '' ? trim($url) : null;
    }

    /**
     * Save WhatsApp URL to both `comlink` and `whatsapp_channel_url` on create and update.
     */
    public function setWhatsappChannelUrlAttribute(?string $value): void
    {
        $clean = is_string($value) && trim($value) !== '' ? trim($value) : null;

        $this->attributes['comlink'] = $clean;

        if (Schema::hasColumn('events', 'whatsapp_channel_url')) {
            $this->attributes['whatsapp_channel_url'] = $clean;
        }
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function checkIns(): HasManyThrough
    {
        return $this->hasManyThrough(CheckIn::class, Registration::class);
    }
}