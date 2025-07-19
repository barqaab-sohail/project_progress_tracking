<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        User::create(

            [
                'name' => 'Sohail Afzal',
                'email' => 'sohail.afzal@barqaab.com',
                'email_verified_at' => now(),
                'password' => bcrypt('Great@786'), // Use a secure password hashing method
                'remember_token' => null
            ]
        );
    }
}
