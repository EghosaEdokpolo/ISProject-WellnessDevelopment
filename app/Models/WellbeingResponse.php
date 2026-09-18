<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WellbeingResponse extends Model
{
    protected $fillable = [
        'registration_id', 'stage',
        'item_cheerful', 'item_calm', 'item_active', 'item_rested', 'item_interested',
        'total_score',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    // POINTER: a static "factory" helper — instead of every controller
    // manually adding up the 5 items, they call WellbeingResponse::score([...])
    // and get back the total. One place to change the WHO-5 math if it ever needs it.
    public static function score(array $items): int
    {
        return array_sum($items); // each item is 0-4, five items => max 20... 
        // NOTE: WHO-5 is scored 0-25 by convention (raw 0-20 x 1.25). If you want
        // the exact WHO-5 standard, multiply the sum by 1.25 here before returning.
    }
}
