<?php


namespace App\Services;


use App\Models\Transactions;

class TransactionsService
{
    public function getTransactionsForLoggedInUser($loggedInUser, $from = null, $to = null)
    {
        $transactions = Transactions::where('user_id', $loggedInUser)
        ->when(($from > 1), function ($query) use($from, $to){
                return $query->whereBetween('transaction_amount',[$from, $to]);
        })
        ->orderBy('created_at','desc')
        ->simplePaginate(10);

        return $transactions;
    }
}
