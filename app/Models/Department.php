<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = ['name', 'code', 'faculty'];

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }
}