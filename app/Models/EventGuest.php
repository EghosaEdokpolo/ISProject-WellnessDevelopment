<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventGuest extends Model
{
    protected $fillable = [
        'event_id',
        'full_name',
        'email',
        'phone',
        'guest_type',
        'organization',
        'invited_by',
        'waiver_accepted',
        'verification_method',
        'verified_at',
        'ip_address',
        'checked_in_at',
    ];

    protected $casts = [
        'waiver_accepted' => 'boolean',
        'verified_at'     => 'datetime',
        'checked_in_at'   => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getGuestTypeLabelAttribute(): string
    {
        return match ($this->guest_type) {
            'facilitator'      => 'Facilitator / Speaker',
            'partner_vendor'   => 'External Partner / Vendor',
            'visiting_faculty' => 'Visiting Faculty',
            'staff_family'     => 'Staff Guest / Family',
            'new_hire_pending' => 'New Staff / Intern (Pending ID)',
            default            => 'External Visitor',
        };
    }
}