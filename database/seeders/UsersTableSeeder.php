<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // DB::table('users')->truncate();

        DB::table('users')->insert([
            'name' => 'Pitch Burners',
            'email' => 'pitchburners@gmail.com',
            'password' => Hash::make('$2y$10$hfONeUOtvBhtj0KDQIfkpecsjX5BpuIy3rJLHbR4xlZN11aY8SqFe'),
            'phone_no' => '8870252283',
            'role' => 1,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
