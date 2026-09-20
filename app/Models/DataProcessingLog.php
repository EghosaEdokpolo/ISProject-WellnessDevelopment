<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataProcessingLog extends Model
{
    protected $fillable = ['staff_id', 'action', 'subject_type', 'subject_id', 'metadata', 'ip_address'];
    protected $casts = ['metadata' => 'array'];

    public function staff(): BelongsTo { return $this->belongsTo(Staff::class); }

    public static function record(string $action, array $metadata = [], ?int $staffId = null): void
    {
        static::create([
            'staff_id'   => $staffId ?? auth()->id(),
            'action'     => $action,
            'metadata'   => $metadata,
            'ip_address' => request()->ip(),
        ]);
    }
}