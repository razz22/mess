<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'nabid@admin.com'],
            [
                'name'           => 'Nabid',
                'email'          => 'nabid@admin.com',
                'password'       => Hash::make('12345678'),
                'is_active'      => true,
                'is_super_admin' => true,
            ]
        );

        $this->command->info('Super admin created: nabid@admin.com');
    }
}
