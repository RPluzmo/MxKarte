<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@mxkarte.lv'],
            [
                'name' => 'Admin',
                'surname' => 'Admin',
                'password' => Hash::make('1'),
                'role' => 'admin',
                'category' => null,
                'experience_level' => null,
            ]
        );
    }
}
