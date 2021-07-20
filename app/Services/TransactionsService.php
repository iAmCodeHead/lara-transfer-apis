<?php


namespace App\Services;


use App\Models\Transactions;

class TransactionsService
{
    public function getTransactionsForLoggedInUser($loggedInUser, $amount = null)
    {
        $transactions = Transactions::where('user_id', $loggedInUser)
                        ->when($amount, function ($query) use($amount){
                             return $query->where('transaction_amount', 'like', '%' . $amount . '%');
                        })
                        ->orderBy('created_at','desc')
                        ->simplePaginate(10);

        return [
            'status' => true,
            'statusCode' => 200,
            'message' => 'All transactions',
            'data' => $transactions
        ];
    }
}
