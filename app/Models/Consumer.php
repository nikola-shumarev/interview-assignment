<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'total_credit_amount'];

    public function bankCredits()
    {
        return $this->hasMany(BankCredit::class);
    }
}
