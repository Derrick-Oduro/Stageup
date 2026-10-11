<?php

namespace App\Models;

use App\Models\User;
use Database\Factories\FixtureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Fixture extends Model
{
    /** @use HasFactory<FixtureFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'tournament_id',
        'round_name',
        'status',
        'scheduled_at',
        'reporting_deadline',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'reporting_deadline' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Tournament, $this>
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'fixture_participants')
            ->withPivot(['side'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<ScoreSubmission, $this>
     */
    public function scoreSubmissions(): HasMany
    {
        return $this->hasMany(ScoreSubmission::class);
    }

    /**
     * @return HasOne<OfficialResult, $this>
     */
    public function officialResult(): HasOne
    {
        return $this->hasOne(OfficialResult::class);
    }

    public function submitScore(User $user, int $scoreFor, int $scoreAgainst, ?string $notes = null): ScoreSubmission
    {
        if (! $this->participants()->where('users.id', $user->id)->exists()) {
            throw new \RuntimeException('The user is not a participant in this fixture.');
        }

        $submission = ScoreSubmission::updateOrCreate(
            [
                'fixture_id' => $this->id,
                'submitted_by_user_id' => $user->id,
            ],
            [
                'score_for' => $scoreFor,
                'score_against' => $scoreAgainst,
                'notes' => $notes,
                'status' => 'submitted',
            ],
        );

        $this->evaluateScoreSubmissions();

        return $submission;
    }

    public function evaluateScoreSubmissions(): self
    {
        $submissions = $this->scoreSubmissions()->get();

        if ($submissions->count() < 2) {
            $this->update(['status' => 'awaiting_scores']);

            return $this;
        }

        $scorePairs = $submissions->map(fn (ScoreSubmission $submission) => [
            'submitted_by_user_id' => $submission->submitted_by_user_id,
            'score_for' => $submission->score_for,
            'score_against' => $submission->score_against,
        ]);

        $first = $scorePairs->first();
        $matches = $scorePairs->every(fn ($submission) => $submission['score_for'] === $first['score_for']
            && $submission['score_against'] === $first['score_against']);

        $this->update([
            'status' => $matches ? 'awaiting_verification' : 'disputed',
        ]);

        return $this;
    }

    public function approveResult(User $approvedBy, User $winner, User $loser, int $scoreFor, int $scoreAgainst, string $reason): OfficialResult
    {
        if (! $this->participants()->where('users.id', $winner->id)->exists()) {
            throw new \RuntimeException('The winner must be a participant in this fixture.');
        }

        if (! $this->participants()->where('users.id', $loser->id)->exists()) {
            throw new \RuntimeException('The loser must be a participant in this fixture.');
        }

        return DB::transaction(function () use ($approvedBy, $winner, $loser, $scoreFor, $scoreAgainst, $reason): OfficialResult {
            $result = OfficialResult::updateOrCreate(
                ['fixture_id' => $this->id],
                [
                    'winner_user_id' => $winner->id,
                    'loser_user_id' => $loser->id,
                    'score_for' => $scoreFor,
                    'score_against' => $scoreAgainst,
                    'approved_by_user_id' => $approvedBy->id,
                    'reason' => $reason,
                ],
            );

            $this->update(['status' => 'verified']);

            return $result;
        });
    }
}
