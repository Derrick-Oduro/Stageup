<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'actor_user_id',
        'subject_type',
        'subject_id',
        'action',
        'metadata',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public static function record(
        ?User $actor,
        string $subjectType,
        int|string $subjectId,
        string $action,
        array $metadata = [],
        ?string $ipAddress = null,
    ): self {
        return self::query()->create([
            'actor_user_id' => $actor?->id,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'action' => $action,
            'metadata' => $metadata,
            'ip_address' => $ipAddress,
            'created_at' => now(),
        ]);
    }
}
