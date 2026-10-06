<?php

namespace App\Models;

use App\Enums\RecurringFrequency;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'payment_method_id',
        'type',
        'title',
        'amount',
        'description',
        'frequency',
        'start_date',
        'end_date',
        'next_run_date',
        'last_run_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type'          => TransactionType::class,
            'frequency'     => RecurringFrequency::class,
            'amount'        => 'decimal:2',
            'start_date'    => 'date',
            'end_date'      => 'date',
            'next_run_date' => 'date',
            'last_run_date' => 'date',
            'is_active'     => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function scopeOwnedBy(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDue(Builder $query): Builder
    {
        return $query->where('is_active', true)
                     ->where(function (Builder $q) {
                         $q->whereNull('end_date')
                           ->orWhereDate('end_date', '>=', now()->toDateString());
                     })
                     ->whereDate('next_run_date', '<=', now()->toDateString());
    }

    /**
     * Advance next_run_date by exactly one step of the frequency.
     */
    public function advanceNextRunDate(): void
    {
        $from = $this->next_run_date ?? now();

        $this->next_run_date = match ($this->frequency) {
            RecurringFrequency::Daily   => $from->copy()->addDay(),
            RecurringFrequency::Weekly  => $from->copy()->addWeek(),
            RecurringFrequency::Monthly => $from->copy()->addMonthNoOverflow(),
            RecurringFrequency::Yearly  => $from->copy()->addYear(),
        };

        $this->save();
    }

    /**
     * Does the entry still make sense (end_date not passed)?
     */
    public function isStillRunning(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->end_date && $this->end_date->isPast() && ! $this->end_date->isToday()) {
            return false;
        }

        return true;
    }

    /**
     * Suggested next_run_date based on start_date + frequency (used at creation).
     */
    public static function firstRunDate(string $startDate, RecurringFrequency $frequency): string
    {
        // The first run is on the start date itself — the command will handle it
        // on the next scheduler pass.
        return $startDate;
    }
}