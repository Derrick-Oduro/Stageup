<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'display_name' => fake()->name(),
            'bio' => fake()->sentence(),
            'timezone' => 'UTC',
            'avatar_url' => null,
            'is_public' => true,
        ];
    }
}
