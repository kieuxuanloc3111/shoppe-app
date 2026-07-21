<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Chỉ seed 1 tài khoản admin (để đăng nhập admin sau migrate:fresh).
     * Không seed data rác. idempotent — chạy nhiều lần không nhân bản.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('123456'),
                'role'     => 'admin',
            ]
        );
    }
}
