<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Fixture;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'totalTournaments' => Tournament::count(),
            'openRegistrations' => Tournament::where('status', 'registration_open')->count(),
            'liveTournaments' => Tournament::whereIn('status', ['scheduled', 'in_progress'])->count(),
        ]);
    }

    public function tournaments(): View
    {
        $tournaments = Tournament::query()
            ->with('organizer:id,name')
            ->orderByDesc('starts_at')
            ->paginate(15);

        return view('admin.tournaments', [
            'tournaments' => $tournaments,
        ]);
    }

    public function transitionStatus(Request $request, Tournament $tournament): RedirectResponse
    {
        $status = $request->validate([
            'status' => ['required', 'string', Rule::in(array_keys(Tournament::STATUS_TRANSITIONS))],
        ])['status'];

        if (! $tournament->canTransitionTo($status)) {
            abort(422, 'This tournament status transition is not allowed.');
        }

        $fromStatus = $tournament->status;
        $tournament->transitionTo($status);

        AuditLog::record(
            auth()->user(),
            Tournament::class,
            $tournament->id,
            'status_changed',
            ['from' => $fromStatus, 'to' => $status],
            $request->ip(),
        );

        return redirect()->route('admin.tournaments')->with('success', 'Tournament status updated.');
    }

    public function fixtures(): View
    {
        $fixtures = Fixture::query()
            ->with(['tournament', 'participants'])
            ->where('status', 'awaiting_verification')
            ->orderByDesc('scheduled_at')
            ->paginate(15);

        return view('admin.fixtures', [
            'fixtures' => $fixtures,
        ]);
    }

    public function verifyFixture(Request $request, Fixture $fixture): RedirectResponse
    {
        $validated = $request->validate([
            'winner_user_id' => ['required', 'exists:users,id'],
            'loser_user_id' => ['required', 'exists:users,id'],
            'score_for' => ['required', 'integer', 'min:0'],
            'score_against' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        if (! $fixture->participants()->where('users.id', $validated['winner_user_id'])->exists()) {
            abort(422, 'The winner must be a participant in this fixture.');
        }

        if (! $fixture->participants()->where('users.id', $validated['loser_user_id'])->exists()) {
            abort(422, 'The loser must be a participant in this fixture.');
        }

        $winner = User::findOrFail($validated['winner_user_id']);
        $loser = User::findOrFail($validated['loser_user_id']);

        $fixture->approveResult(
            auth()->user(),
            $winner,
            $loser,
            $validated['score_for'],
            $validated['score_against'],
            $validated['reason'],
        );

        AuditLog::record(
            auth()->user(),
            Fixture::class,
            $fixture->id,
            'official_result_approved',
            [
                'winner_user_id' => $winner->id,
                'loser_user_id' => $loser->id,
                'score_for' => $validated['score_for'],
                'score_against' => $validated['score_against'],
            ],
            $request->ip(),
        );

        return redirect()->route('admin.fixtures')->with('success', 'Official result approved.');
    }
}
