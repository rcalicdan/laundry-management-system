<?php

namespace App\Enums;

enum UserRoles: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case EMPLOYEE = 'employee';

    public static function getAllRoles(): array
    {
        return array_map(fn(UserRoles $role) => $role->value, self::cases());
    }
}
