<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NetWorthSnapshot extends Model
{
    protected $fillable = ['user_id', 'date', 'total', 'by_account'];

    protected $casts = [
        'date' => 'date',
        'total' => 'decimal:2',
        'by_account' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
