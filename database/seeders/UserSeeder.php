<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Address;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(1)->create()->each(
            function ($user) {
                $user->assignRole('admin');
            }
        );
        User::factory()->count(1)->create()->each(
            function ($user) {
                $user->assignRole('seller');
            }
        );
        User::factory()->count(1)->create()->each(
            function ($user) {
                $user->assignRole('costumer');
            }
        );
    }
}
