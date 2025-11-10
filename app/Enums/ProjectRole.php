<?php

namespace App\Enums;

enum ProjectRole: string
{
    case OWNER = 'owner';
    case MANAGER = 'manager';
    case DEVELOPER = 'developer';
    case TESTER = 'tester';
    case VIEWER = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'Owner',
            self::MANAGER => 'Manager',
            self::DEVELOPER => 'Developer',
            self::TESTER => 'Tester',
            self::VIEWER => 'Viewer',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
