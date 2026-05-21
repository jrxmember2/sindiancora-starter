<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('SUPERADMIN_EMAIL', 'admin@sindiancora.local')],
            [
                'name' => env('SUPERADMIN_NAME', 'Superadmin'),
                'password' => Hash::make(env('SUPERADMIN_PASSWORD', 'password')),
                'status' => 'active',
                'is_superadmin' => true,
            ]
        );
    }
}
