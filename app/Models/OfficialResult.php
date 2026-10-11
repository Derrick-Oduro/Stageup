<?php

namespace App\Models;

use App\Models\User;
use Database\Factories\OfficialResultFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficialResult extends Model
{
    /** @use HasFactory<OfficialResultFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'fixture_id',
        'winner_user_id',
        'loser_user_id',
        'score_for',
        'score_against',
        'approved_by_user_id',
        'reason',
    ];

    /**
     * @return BelongsTo<Fixture, $this>
     */
    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function loser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'loser_user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
}
