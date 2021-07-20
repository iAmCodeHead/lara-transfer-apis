<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'transaction_type',
        'transaction_amount',
        'transaction_status',
        'transfer_code',
        'reason',
        'transaction_reference',
        'account_balance'
    ];
}
