<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['bank_credit_id', 'amount', 'payment_date'];

    protected $casts = [
        'payment_date' => 'datetime'
    ];

    public function bankCredit(): BelongsTo
    {
        return $this->belongsTo(BankCredit::class);
    }
}
