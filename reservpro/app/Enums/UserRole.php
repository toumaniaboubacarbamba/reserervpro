<?php
namespace App\Enums;

enum UserRole: string{
    case Admin = 'admin';
    case Gerant = 'gerant';
    case Client = 'client';
}
