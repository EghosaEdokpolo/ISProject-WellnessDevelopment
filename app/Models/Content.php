<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Content extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'category',
        'excerpt',
        'body',
        'image_url',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function savedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'content_user')->withTimestamps();
    }
}