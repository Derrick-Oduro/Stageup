<?php

namespace Database\Factories;

use App\Models\Fixture;
use App\Models\ScoreSubmission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScoreSubmission>
 */
class ScoreSubmissionFactory extends Factory
{
    protected $model = ScoreSubmission::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fixture_id' => Fixture::factory(),
            'submitted_by_user_id' => User::factory(),
            'score_for' => fake()->numberBetween(0, 5),
            'score_against' => fake()->numberBetween(0, 5),
            'notes' => fake()->sentence(),
            'status' => 'submitted',
        ];
    }
}
