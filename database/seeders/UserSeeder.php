<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
        {
            User::updateOrCreate(
                ['email' => 'admin@mxkarte.lv'],
                [
                    'name' => 'Admin',
                    'surname' => 'Admin',
                    'password' => Hash::make('1'),
                    'role' => 'admin',
                    'category' => '',
                    'experience_level' => '',
                ]
            );
        }
}