<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_SELLER =0;
    const ROLE_ADMIN = 1;
    const ROLE_BUYER =2;


    use Notifiable;
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'avatar',
        'id_country',
        'level',
    ];

    protected $hidden = [
        'password',
    ];

        // Helper functions
    public function isAdmin()
    {
        return $this->level == self::ROLE_ADMIN;
    }

    public function isSeller()
    {
        return $this->level == self::ROLE_SELLER;
    }

    public function isBuyer()
    {
        return $this->level == self::ROLE_BUYER;
    }

    // 
    public function getRoleNameAttribute()
    {
        return match ($this->level) {
            self::ROLE_ADMIN => 'admin',
            self::ROLE_SELLER => 'seller',
            self::ROLE_BUYER => 'buyer',
            default => 'unknown',
        };
    }
}
