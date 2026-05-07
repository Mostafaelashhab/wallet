<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseSplit extends Model
{
    public $timestamps = false;

    protected $fillable = ['expense_id', 'user_id', 'amount', 'share_value', 'settled_at'];

    protected $casts = [
        'amount' => 'decimal:2',
        'share_value' => 'decimal:4',
        'settled_at' => 'datetime',
    ];

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
