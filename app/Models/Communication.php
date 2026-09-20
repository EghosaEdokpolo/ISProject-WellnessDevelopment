<?php

namespace App\Models;

use App\Enums\CommunicationChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Communication extends Model
{
    protected $fillable = [
        'event_id', 'channel', 'subject', 'body', 'recipient_filter',
        'sent_at', 'status', 'recipient_count', 'sent_by',
    ];

    protected $casts = [
        'channel'          => CommunicationChannel::class,
        'recipient_filter' => 'array',
        'sent_at'          => 'datetime',
    ];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function sender(): BelongsTo { return $this->belongsTo(Staff::class, 'sent_by'); }
}