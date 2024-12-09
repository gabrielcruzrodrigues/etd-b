<?php

namespace App\Enums;

enum UserRole: string {
    case ADMIN = 'admin';
    case SUPPORT = 'suporte';
    case COMMON = 'comum';
}
