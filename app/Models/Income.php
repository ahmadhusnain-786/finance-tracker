<?php

namespace App\Models;

use App\Enums\RecurringFrequency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    use HasFactory;

    /**
     * Laravel would guess "incomes" as the table name. Our table is "income".
     * So we tell it explicitly.
     */
    protected $table = 'income';

    protected $fillable = [
        'user_id',
        'category_id',
        'payment_method_id',
        'title',
        'amount',
        'date',
        'description',
        'is_recurring',
        'recurring_frequency',
    ];

    protected function casts(): array
    {
        return [
            'amount'              => 'decimal:2',
            'date'                => 'date',
            'is_recurring'        => 'boolean',
            'recurring_frequency' => RecurringFrequency::class,
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

    public function scopeBetweenDates(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('date', [$from, $to]);
    }
}
