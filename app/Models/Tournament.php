<?php

namespace App\Models;

use Database\Factories\TournamentFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    /** @use HasFactory<TournamentFactory> */
    use HasFactory;

    public const STATUS_TRANSITIONS = [
        'draft' => ['registration_open', 'cancelled'],
        'upcoming' => ['registration_open', 'cancelled'],
        'registration_open' => ['registration_closed', 'cancelled'],
        'registration_closed' => ['scheduled', 'cancelled'],
        'scheduled' => ['in_progress', 'cancelled'],
        'in_progress' => ['completed', 'cancelled'],
        'completed' => [],
        'cancelled' => [],
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'organizer_id',
        'name',
        'slug',
        'game',
        'format',
        'status',
        'registration_deadline',
        'starts_at',
        'max_participants',
        'participants_count',
        'followers_count',
        'is_public',
        'rules',
        'format_config',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'registration_deadline' => 'datetime',
            'starts_at' => 'datetime',
            'is_public' => 'boolean',
            'format_config' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    #[Scope]
    protected function public(Builder $query): void
    {
        $query->where('is_public', true);
    }

    #[Scope]
    protected function withFilters(Builder $query, array $filters): void
    {
        $query
            ->when(
                filled($filters['q'] ?? null),
                fn (Builder $builder) => $builder->where(function (Builder $subQuery) use ($filters): void {
                    $subQuery
                        ->where('name', 'like', '%'.$filters['q'].'%')
                        ->orWhere('game', 'like', '%'.$filters['q'].'%');
                }),
            )
            ->when(
                filled($filters['game'] ?? null),
                fn (Builder $builder) => $builder->where('game', $filters['game']),
            )
            ->when(
                filled($filters['status'] ?? null),
                fn (Builder $builder) => $builder->where('status', $filters['status']),
            );
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tournament_followers')->withTimestamps();
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tournament_participants')->withTimestamps();
    }

    public function canTransitionTo(string $status): bool
    {
        $allowedTransitions = self::STATUS_TRANSITIONS[$this->status] ?? [];

        return in_array($status, $allowedTransitions, true);
    }

    public function transitionTo(string $status): self
    {
        if (! $this->canTransitionTo($status)) {
            throw new \InvalidArgumentException(sprintf(
                'Tournament status cannot transition from "%s" to "%s".',
                $this->status,
                $status,
            ));
        }

        $this->status = $status;
        $this->save();

        return $this;
    }

    /**
     * @return HasMany<Fixture, $this>
     */
    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }
}
