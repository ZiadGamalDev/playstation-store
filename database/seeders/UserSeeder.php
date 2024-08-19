<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@nsh7nha.com',
            'is_admin' => true
        ]);

        User::factory()->create([
            'name' => 'Ziad Gamal',
            'email' => 'zyadgamal450@gmail.com',
        ]);
    }
}
