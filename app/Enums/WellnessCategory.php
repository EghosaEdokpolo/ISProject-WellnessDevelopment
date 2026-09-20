<?php

namespace App\Enums;

enum WellnessCategory: string
{
    case Physical  = 'physical';
    case Mental    = 'mental';
    case Financial = 'financial';
    case Social    = 'social';

    public function label(): string
    {
        return match ($this) {
            self::Physical  => 'Physical',
            self::Mental    => 'Mental',
            self::Financial => 'Financial',
            self::Social    => 'Social',
        };
    }
}