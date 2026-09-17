<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Waiver extends Model
{
    protected $fillable = ['registration_id', 'signature_name', 'signed_at', 'ip_address'];

    protected function casts(): array
    {
        return ['signed_at' => 'datetime'];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
