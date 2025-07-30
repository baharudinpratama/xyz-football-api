<?php

namespace Database\Seeders;

use App\Models\Goal;
use App\Models\MatchResult;
use App\Models\MatchSchedule;
use App\Models\Player;
use App\Models\Team;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MatchScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = FakerFactory::create('id_ID');
        $teams = Team::all();

        for ($i = 0; $i < 20; $i++) {
            $homeTeam = $teams->random();
            do {
                $awayTeam = $teams->random();
            } while ($awayTeam->id === $homeTeam->id);

            $match = MatchSchedule::create([
                'match_date' => $faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
                'match_time' => $faker->time('H:i'),
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
            ]);

            if ($faker->boolean(50)) {
                $homeScore = $faker->numberBetween(0, 5);
                $awayScore = $faker->numberBetween(0, 5);

                MatchResult::create([
                    'match_schedule_id' => $match->id,
                    'home_score' => $homeScore,
                    'away_score' => $awayScore,
                ]);

                $totalGoals = $homeScore + $awayScore;

                for ($g = 0; $g < $totalGoals; $g++) {
                    $team = $faker->boolean(50) ? $homeTeam : $awayTeam;
                    $player = Player::where('team_id', $team->id)->inRandomOrder()->first();

                    Goal::create([
                        'match_schedule_id' => $match->id,
                        'team_id' => $team->id,
                        'player_id' => $player->id,
                        'minute' => $faker->numberBetween(1, 90),
                    ]);
                }
            }
        }
    }
}
