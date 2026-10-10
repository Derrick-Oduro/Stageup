<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TournamentController extends Controller
{
    /**
     * Display public tournaments with lightweight filtering.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'game', 'status']);
        $userId = $request->user()?->id;

        $tournamentsQuery = Tournament::query()
            ->public()
            ->with('organizer:id,name')
            ->withCount([
                'followers as real_followers_count',
                'participants as real_participants_count',
            ])
            ->withFilters($filters)
            ->orderByRaw("case status
                when 'registration_open' then 1
                when 'upcoming' then 2
                when 'in_progress' then 3
                when 'completed' then 4
                else 5
            end")
            ->orderBy('starts_at');

        if ($userId) {
            $tournamentsQuery->withExists([
                'followers as is_followed' => fn ($query) => $query->where('users.id', $userId),
                'participants as is_joined' => fn ($query) => $query->where('users.id', $userId),
            ]);
        }

        $tournaments = $tournamentsQuery
            ->paginate(9)
            ->withQueryString();

        $games = Tournament::query()
            ->public()
            ->orderBy('game')
            ->distinct()
            ->pluck('game');

        return view('tournaments.index', [
            'tournaments' => $tournaments,
            'games' => $games,
            'filters' => $filters,
        ]);
    }

    /**
     * Display a public tournament detail page.
     */
    public function show(Tournament $tournament): View
    {
        abort_unless($tournament->is_public, 404);

        $tournament->load('organizer:id,name')->loadCount([
            'followers as real_followers_count',
            'participants as real_participants_count',
        ]);

        $isFollowed = auth()->check()
            ? $tournament->followers()->where('users.id', auth()->id())->exists()
            : false;

        $isJoined = auth()->check()
            ? $tournament->participants()->where('users.id', auth()->id())->exists()
            : false;

        return view('tournaments.show', [
            'tournament' => $tournament,
            'isFollowed' => $isFollowed,
            'isJoined' => $isJoined,
        ]);
    }

    public function follow(Tournament $tournament): RedirectResponse
    {
        abort_unless($tournament->is_public, 404);

        $userId = auth()->id();

        DB::transaction(function () use ($tournament, $userId): void {
            $lockedTournament = Tournament::query()->whereKey($tournament->id)->lockForUpdate()->firstOrFail();

            if (! $lockedTournament->followers()->where('users.id', $userId)->exists()) {
                $lockedTournament->followers()->attach($userId);
                $lockedTournament->update([
                    'followers_count' => $lockedTournament->followers()->count(),
                ]);
            }
        });

        return back();
    }

    public function unfollow(Tournament $tournament): RedirectResponse
    {
        abort_unless($tournament->is_public, 404);

        $userId = auth()->id();

        DB::transaction(function () use ($tournament, $userId): void {
            $lockedTournament = Tournament::query()->whereKey($tournament->id)->lockForUpdate()->firstOrFail();

            if ($lockedTournament->followers()->where('users.id', $userId)->exists()) {
                $lockedTournament->followers()->detach($userId);
                $lockedTournament->update([
                    'followers_count' => $lockedTournament->followers()->count(),
                ]);
            }
        });

        return back();
    }

    public function join(Tournament $tournament): RedirectResponse
    {
        abort_unless($tournament->is_public, 404);

        $userId = auth()->id();

        DB::transaction(function () use ($tournament, $userId): void {
            $lockedTournament = Tournament::query()->whereKey($tournament->id)->lockForUpdate()->firstOrFail();

            if ($lockedTournament->participants()->where('users.id', $userId)->exists()) {
                return;
            }

            if (! $this->canJoin($lockedTournament)) {
                return;
            }

            $lockedTournament->participants()->attach($userId);
            $lockedTournament->update([
                'participants_count' => $lockedTournament->participants()->count(),
            ]);
        });

        return back();
    }

    public function leave(Tournament $tournament): RedirectResponse
    {
        abort_unless($tournament->is_public, 404);

        $userId = auth()->id();

        DB::transaction(function () use ($tournament, $userId): void {
            $lockedTournament = Tournament::query()->whereKey($tournament->id)->lockForUpdate()->firstOrFail();

            if ($lockedTournament->participants()->where('users.id', $userId)->exists()) {
                $lockedTournament->participants()->detach($userId);
                $lockedTournament->update([
                    'participants_count' => $lockedTournament->participants()->count(),
                ]);
            }
        });

        return back();
    }

    private function canJoin(Tournament $tournament): bool
    {
        if ($tournament->status !== 'registration_open') {
            return false;
        }

        if ($tournament->registration_deadline && now()->greaterThan($tournament->registration_deadline)) {
            return false;
        }

        return $tournament->participants()->count() < $tournament->max_participants;
    }
}
