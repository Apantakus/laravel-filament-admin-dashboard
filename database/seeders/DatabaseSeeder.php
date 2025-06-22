<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Department;
use App\Models\Team;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin1@admin.com',
            'password'=> bcrypt('sunsen123'),
            'is_admin'=> true,
        ]);

        $team = Team::create([
        'name' => 'Test Team',
        'slug' => 'test-team',
    ]);

        $team->members()->attach($user);
        $this->call(CountriesTableSeeder::class);
        $this->call(StatesTableSeeder::class);
        $this->call(CitiesTableSeeder::class);
       Department::create([
        'name' => 'Laravel',
        'team_id' => $team->id,
    ]);
    }

}
