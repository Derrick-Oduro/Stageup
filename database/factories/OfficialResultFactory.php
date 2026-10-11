<?php

namespace Database\Factories;

use App\Models\Fixture;
use App\Models\OfficialResult;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OfficialResult>
 */
class OfficialResultFactory extends Factory
{
    protected $model = OfficialResult::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fixture_id' => Fixture::factory(),
            'winner_user_id' => User::factory(),
            'loser_user_id' => User::factory(),
            'score_for' => fake()->numberBetween(1, 3),
            'score_against' => fake()->numberBetween(0, 2),
            'approved_by_user_id' => User::factory(),
            'reason' => fake()->sentence(),
        ];
    }
}
