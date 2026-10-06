<?php

namespace App\Models;

use App\Enums\GoalStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialGoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'current_amount',
        'target_date',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'target_amount'  => 'decimal:2',
            'current_amount' => 'decimal:2',
            'target_date'    => 'date',
            'status'         => GoalStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(GoalContribution::class);
    }

    public function scopeOwnedBy(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', GoalStatus::Active->value);
    }

    /**
     * Progress as a percentage 0–100. Returns 0 if target is 0.
     */
    public function getProgressPercentageAttribute(): float
    {
        if ((float) $this->target_amount <= 0) {
            return 0.0;
        }

        $pct = ((float) $this->current_amount / (float) $this->target_amount) * 100;

        return min(100.0, round($pct, 2));
    }

    /**
     * Remaining amount to reach the target. Never negative.
     */
    public function getRemainingAmountAttribute(): float
    {
        $remaining = (float) $this->target_amount - (float) $this->current_amount;

        return max(0.0, round($remaining, 2));
    }

    /**
     * Recalculate current_amount from contributions. Call after any change.
     */
    public function recalculateCurrentAmount(): void
    {
        $sum = $this->contributions()->sum('amount');
        $this->current_amount = $sum;

        if ((float) $this->current_amount >= (float) $this->target_amount
            && $this->status === GoalStatus::Active) {
            $this->status = GoalStatus::Completed;
        }

        $this->save();
    }
}