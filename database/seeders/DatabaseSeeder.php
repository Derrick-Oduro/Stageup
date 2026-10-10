<?php

namespace Database\Seeders;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        User::query()
            ->whereDoesntHave('roles')
            ->each(fn (User $existingUser) => $existingUser->assignRole('registered_user'));

        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')],
        );

        $user->assignRole('admin');

        $organizer = User::firstOrCreate(
            ['email' => 'organizer@example.com'],
            ['name' => 'Main Organizer', 'password' => bcrypt('password')],
        );

        $organizer->assignRole('organizer');

        Tournament::query()->delete();

        $players = User::factory()->count(12)->create();
        foreach ($players as $player) {
            $player->assignRole('registered_user');
        }

        $tournaments = Tournament::factory()
            ->count(3)
            ->for($organizer, 'organizer')
            ->create([
                'status' => 'registration_open',
                'participants_count' => 0,
                'followers_count' => 0,
            ]);

        foreach ($tournaments as $tournament) {
            $participantIds = $players
                ->shuffle()
                ->take(random_int(4, min(8, $tournament->max_participants)))
                ->pluck('id')
                ->all();

            $followerIds = collect($participantIds)
                ->merge(
                    $players
                        ->shuffle()
                        ->take(random_int(2, 6))
                        ->pluck('id')
                        ->all(),
                )
                ->unique()
                ->values()
                ->all();

            $tournament->participants()->sync($participantIds);
            $tournament->followers()->sync($followerIds);

            $tournament->update([
                'participants_count' => count($participantIds),
                'followers_count' => count($followerIds),
            ]);
        }
    }
}
