<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PlayersTableSeeder extends Seeder
{
    public function run()
    {
        $battingStyles = ['Right-hand bat', 'Left-hand bat'];
        $bowlingStyles = ['Right-arm fast', 'Left-arm fast', 'Right-arm spin', 'Left-arm spin'];
        $roles = ['Batsman', 'Bowler', 'All-rounder', 'Wicketkeeper'];
        $faker = \Faker\Factory::create();

        $players = [];
        $now = Carbon::now();

        for ($i = 1; $i <= 30; $i++) {
            $players[] = [
                'team_id' => rand(1, 5),
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'empid' => strtoupper(Str::random(8)),
                'phone' => $faker->phoneNumber,
                'image' => $faker->imageUrl(640, 480, 'sports', true, 'players'),
                'batting_style' => $faker->randomElement($battingStyles),
                'bowling_style' => $faker->randomElement($bowlingStyles),
                'role' => $faker->randomElement($roles),
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ];
        }

        DB::table('players')->insert($players);
    }
}
