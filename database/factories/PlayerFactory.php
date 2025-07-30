<?php

namespace Database\Factories;

use App\Models\Team;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = FakerFactory::create('id_ID');
        $positions = ['GK', 'DF', 'MF', 'FW'];

        return [
            'team_id' => Team::inRandomOrder()->first()?->id ?? 1,
            'name' => $faker->name,
            'height' => $faker->numberBetween(160, 190),
            'weight' => $faker->numberBetween(50, 85),
            'position' => $faker->randomElement($positions),
            'number' => null,
        ];
    }
}
