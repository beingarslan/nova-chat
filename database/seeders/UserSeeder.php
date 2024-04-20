<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        $users = [
            [
                'name' => $faker->name(),
                'email' => 'admin@admin.com',
                'password' => Hash::make('1234567890'),
                'is_admin' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(24),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => $faker->name(),
                'email' => 'user@user.com',
                'password' => Hash::make('1234567890'),
                'is_admin' => false,
                'email_verified_at' => now(),
                'remember_token' => Str::random(24),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        for ($i = 0; $i < 10; $i++) {
            $users[] = [
                'name' => $faker->name(),
                'email' => $faker->email(),
                'password' => Hash::make('1234567890'),
                'is_admin' => false,
                'email_verified_at' => now(),
                'remember_token' => Str::random(24),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        User::insert($users);
    }
}
