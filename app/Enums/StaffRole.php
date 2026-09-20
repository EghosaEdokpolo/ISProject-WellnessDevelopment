<?php

namespace App\Enums;

enum StaffRole: string
{
    case Staff               = 'staff';
    case HrAdmin             = 'hr_admin';
    case WellnessCoordinator = 'wellness_coordinator';

    public function canManageEvents(): bool
    {
        return in_array($this, [self::HrAdmin, self::WellnessCoordinator], true);
    }

    public function canResetPoints(): bool
    {
        return $this === self::HrAdmin;
    }
}