<?php

namespace Database\Factories;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tournament>
 */
class TournamentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);
        $maxParticipants = fake()->randomElement([8, 16, 32, 64]);
        $participantsCount = fake()->numberBetween(0, $maxParticipants);
        $status = fake()->randomElement([
            'upcoming',
            'registration_open',
            'in_progress',
            'completed',
        ]);

        $startsAt = match ($status) {
            'completed' => now()->subDays(fake()->numberBetween(3, 30)),
            'in_progress' => now()->subDays(fake()->numberBetween(0, 2)),
            default => now()->addDays(fake()->numberBetween(2, 40)),
        };

        return [
            'organizer_id' => User::factory(),
            'name' => Str::title($title),
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'game' => fake()->randomElement([
                'Valorant',
                'CS2',
                'EA FC 26',
                'Street Fighter 6',
                'Tekken 8',
                'Rocket League',
            ]),
            'format' => fake()->randomElement([
                'single_elimination',
                'double_elimination',
                'round_robin',
            ]),
            'status' => $status,
            'registration_deadline' => in_array($status, ['completed', 'in_progress'], true)
                ? $startsAt->copy()->subDays(fake()->numberBetween(4, 14))
                : now()->addDays(fake()->numberBetween(1, 20)),
            'starts_at' => $startsAt,
            'max_participants' => $maxParticipants,
            'participants_count' => $participantsCount,
            'followers_count' => fake()->numberBetween($participantsCount, $participantsCount + 120),
            'is_public' => true,
            'rules' => fake()->sentence(12),
        ];
    }
}
