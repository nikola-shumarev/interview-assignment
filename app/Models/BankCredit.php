<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'consumer_id',
        'unique_id',
        'amount',
        'remaining_amount',
        'interest_rate',
        'last_interest_applied',
        'due_date'
    ];

    protected $appends = ['unique_id', 'monthly_payment'];

    protected $casts = [
        'due_date' => 'date',
        'last_interest_applied' => 'date'
    ];

    public function consumer(): BelongsTo
    {
        return $this->belongsTo(Consumer::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the unique identifier for the bank credit.
     *
     * @return string
     */
    public function getUniqueIdAttribute(): string
    {
        return str_pad($this->id, 7, '0', STR_PAD_LEFT);
    }

    /**
     * Get the monthly payment amount for the bank credit, calculated by dividing the remaining amount by the remaining months.
     *
     * @return float
     */
    public function getMonthlyPaymentAttribute(): float
    {
        // Check if due_date is not null
        if ($this->due_date) {
            $remainingMonths = $this->due_date->diffInMonths(Carbon::now());

            if ($remainingMonths > 0) {
                $monthlyPayment = $this->remaining_amount / $remainingMonths;
            } else {
                $monthlyPayment = 0; // Handle cases where the due date might have passed or is today
            }

            return $monthlyPayment;
        }

        return null; // Return null if due_date is not provided
    }
}
