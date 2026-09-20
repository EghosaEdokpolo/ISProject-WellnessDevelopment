<?php

namespace App\Models;

use App\Enums\StaffRole;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Staff extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    protected $table = 'staff';

    protected $fillable = [
        'staff_id_number', 'first_name', 'last_name', 'email', 'phone',
        'password', 'department_id', 'role', 'points_balance',
        'consent_given', 'consent_given_at', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password'         => 'hashed',
        'role'             => StaffRole::class,
        'consent_given'    => 'boolean',
        'consent_given_at' => 'datetime',
        'is_active'        => 'boolean',
    ];

    public function department(): BelongsTo { return $this->belongsTo(Department::class); }
    public function attendance(): HasMany { return $this->hasMany(EventAttendance::class); }
    public function assessments(): HasMany { return $this->hasMany(WellnessAssessment::class); }
    public function feedback(): HasMany { return $this->hasMany(Feedback::class); }
    public function pointsTransactions(): HasMany { return $this->hasMany(PointsTransaction::class); }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}