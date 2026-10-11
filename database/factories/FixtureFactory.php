<?php

namespace Database\Factories;

use App\Models\Fixture;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fixture>
 */
class FixtureFactory extends Factory
{
    protected $model = Fixture::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tournament_id' => Tournament::factory(),
            'round_name' => 'Round 1',
            'status' => 'scheduled',
            'scheduled_at' => now()->addDay(),
            'reporting_deadline' => now()->addHours(24),
            'notes' => fake()->sentence(),
        ];
    }
}
