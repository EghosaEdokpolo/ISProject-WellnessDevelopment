<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 
        'staff_id', 
        'email',        
        'password', 
        'role', 
        'department', 
        'points_balance'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isHrCoordinator(): bool
    {
        return $this->role === 'hr_coordinator';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function registrations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function eventsCreated(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function pointsTransactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PointsTransaction::class);
    }
}