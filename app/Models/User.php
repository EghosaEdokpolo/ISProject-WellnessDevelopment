<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password', 'employee_id', 'role', 'department',
    ];

    protected $hidden = ['password', 'remember_token'];

    // POINTER: these two little helper methods let your Blade views say
    // @if($user->isHrCoordinator()) instead of @if($user->role === 'hr_coordinator')
    // everywhere — much easier to read, and if the role logic ever gets more
    // complex (e.g. multiple HR roles) you only change it in one place.
    public function isHrCoordinator(): bool
    {
        return $this->role === 'hr_coordinator';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    // POINTER: hasMany() tells Eloquent (Laravel's ORM) that ONE user can have
    // MANY registrations. Laravel infers it's looking for a `user_id` column
    // on the registrations table because of the naming convention — that's
    // why the foreignId() in the migration was named user_id.
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function eventsCreated(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function pointsTransactions(): HasMany
    {
        return $this->hasMany(PointsTransaction::class);
    }
}
