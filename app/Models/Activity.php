<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = ['user_id', 'action', 'group_id', 'subject_type', 'subject_id', 'meta'];

    protected $casts = ['meta' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public static function log(string $action, array $meta = [], ?int $groupId = null, ?string $subjectType = null, ?int $subjectId = null): self
    {
        return static::create([
            'user_id' => auth()->id() ?? 0,
            'action' => $action,
            'group_id' => $groupId,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'meta' => $meta,
        ]);
    }
}
