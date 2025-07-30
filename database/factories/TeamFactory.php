<?php

namespace Database\Factories;

use App\Models\Team;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    protected $model = Team::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = FakerFactory::create('id_ID');
        $words = ucfirst(implode(' ', $faker->words(2)));
        $number = $faker->unique()->numberBetween(1, 99);

        return [
            'name' => "$words $number",
            'logo' => null,
            'year_founded' => $faker->numberBetween(1900, 2025),
            'address' => $faker->address,
            'city' => $faker->city,
            'is_internal' => $faker->boolean(30),
        ];
    }
}
