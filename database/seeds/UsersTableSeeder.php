<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();

        User::truncate();

        foreach(range(1, 10) as $i) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->email,
                'password' => bcrypt('00000000'),
                'api_token' => str_random(60)
            ]);
        }
    }
}
