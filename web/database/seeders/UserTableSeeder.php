<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

/**
 * 참고 https://dev.to/shanisingh03/generate-dummy-laravel-data-with-model-factories-seeder-gg4
 */
class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::factory()->create();
    }
}
