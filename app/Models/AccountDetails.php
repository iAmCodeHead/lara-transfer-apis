<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountDetails extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'account_name', 'account_number', 'account_balance', 'bank_name'];

}
