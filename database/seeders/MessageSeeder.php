<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        $admin = User::admin()->first();
        $userIDs = User::user()->pluck('id')->toArray();

        foreach ($userIDs as $userID) {
            for ($i = 0; $i < rand(10,200); $i++) {
                $fromID = null;
                $toID = null;
                if(rand(0,1)){
                    $fromID = $admin->id;
                    $toID = $userID;
                }
                else{
                    $fromID = $userID;
                    $toID = $admin->id;
                }
                // random days between 1 month and yesterday
                $messages[] = [
                    'from_id' => $fromID,
                    'to_id' => $toID,
                    'body' => $faker->text(rand(10, 200)),
                    'created_at' => $faker->dateTimeBetween('-' . rand(1, 30) . ' days'),
                    'updated_at' => $faker->dateTimeBetween('-' . rand(1, 30) . ' days'),
                    'seen_at' => rand(0, 1) ? now() : null,
                ];
            }
        }

        Message::insert($messages);
    }
}
