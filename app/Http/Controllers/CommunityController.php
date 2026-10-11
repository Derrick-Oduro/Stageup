<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\CommunityMessage;
use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunityController extends Controller
{
    public function fixtures(): View
    {
        $fixtures = Fixture::query()
            ->whereHas('tournament', fn ($query) => $query->where('is_public', true))
            ->with(['tournament', 'participants', 'officialResult.winner', 'officialResult.loser'])
            ->orderByRaw("case status when 'scheduled' then 1 when 'awaiting_scores' then 2 when 'disputed' then 3 when 'verified' then 4 else 5 end")
            ->orderBy('scheduled_at')
            ->paginate(12);

        return view('community.fixtures', compact('fixtures'));
    }

    public function table(Request $request): View
    {
        $tournaments = Tournament::query()
            ->public()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $selectedTournament = $request->string('tournament')->toString();
        $tournament = $tournaments->firstWhere('slug', $selectedTournament) ?? $tournaments->first();

        $fixtures = Fixture::query()
            ->when($tournament, fn ($query) => $query->where('tournament_id', $tournament->id))
            ->where('status', 'verified')
            ->with('officialResult.winner', 'officialResult.loser')
            ->get();

        $standings = [];

        foreach ($fixtures as $fixture) {
            $result = $fixture->officialResult;

            if (! $result) {
                continue;
            }

            foreach ([$result->winner, $result->loser] as $player) {
                $standings[$player->id] ??= [
                    'player' => $player,
                    'played' => 0,
                    'wins' => 0,
                    'losses' => 0,
                    'goals_for' => 0,
                    'goals_against' => 0,
                    'points' => 0,
                ];

                $standings[$player->id]['played']++;
            }

            $winner = &$standings[$result->winner_user_id];
            $loser = &$standings[$result->loser_user_id];

            $winner['wins']++;
            $winner['goals_for'] += $result->score_for;
            $winner['goals_against'] += $result->score_against;
            $winner['points'] += 3;

            $loser['losses']++;
            $loser['goals_for'] += $result->score_against;
            $loser['goals_against'] += $result->score_for;

            unset($winner, $loser);
        }

        $standings = collect($standings)
            ->map(function (array $row): array {
                $row['difference'] = $row['goals_for'] - $row['goals_against'];

                return $row;
            })
            ->sortByDesc(fn (array $row) => [$row['points'], $row['difference'], $row['wins']])
            ->values();

        return view('community.table', compact('standings', 'tournaments', 'tournament'));
    }

    public function community(): View
    {
        $tournaments = Tournament::query()
            ->public()
            ->with('organizer:id,name')
            ->withCount('fixtures')
            ->orderByDesc('starts_at')
            ->limit(6)
            ->get();

        $recentFixtures = Fixture::query()
            ->whereHas('tournament', fn ($query) => $query->where('is_public', true))
            ->with(['tournament', 'officialResult.winner', 'officialResult.loser'])
            ->where('status', 'verified')
            ->latest('updated_at')
            ->limit(5)
            ->get();

        $messages = CommunityMessage::query()
            ->with('user:id,name')
            ->latest('created_at')
            ->latest('id')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        return view('community.room', compact('tournaments', 'recentFixtures', 'messages'));
    }

    public function storeMessage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'min:1', 'max:1000'],
        ]);

        CommunityMessage::create([
            'user_id' => $request->user()->id,
            'body' => $validated['body'],
        ]);

        return redirect()->to(route('community.index').'#chat');
    }
}