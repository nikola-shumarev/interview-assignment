<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consumer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'total_credit_amount'];

    public function bankCredits(): HasMany
    {
        return $this->hasMany(BankCredit::class);
    }
}
