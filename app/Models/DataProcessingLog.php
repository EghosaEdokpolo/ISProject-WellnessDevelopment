<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataProcessingLog extends Model
{
    protected $fillable = ['user_id', 'action', 'subject_type', 'subject_id', 'metadata', 'ip_address'];
    protected $casts = ['metadata' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $action, array $metadata = [], ?int $userId = null): void
    {
        static::create([
            'user_id'    => $userId ?? auth()->id(),
            'action'     => $action,
            'metadata'   => $metadata,
            'ip_address' => request()->ip(),
        ]);
    }
}