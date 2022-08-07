<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com'
        ])->assignRole('admin');

        User::factory()->create([
            'name' => 'Editor',
            'email' => 'editor@test.com'
        ])->assignRole('editor');

        User::factory()->create([
            'name' => 'Commentator',
            'email' => 'commentator@test.com',
        ])->assignRole('commentator');

        User::factory()->create([
            'name' => 'Unknown',
            'email' => 'unknown@test.com',
        ]);
    }
}