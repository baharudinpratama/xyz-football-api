<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = Team::all();

        foreach ($teams as $team) {
            for ($i = 1; $i <= 15; $i++) {
                Player::factory()->create([
                    'team_id' => $team->id,
                    'number' => $i,
                ]);
            }
        }
    }
}
