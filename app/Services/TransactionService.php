<?php


namespace App\Services;


use App\Models\Transactions;

class TransactionService
{
    public function getTransactionsForLoggedInUser($loggedInUser)
    {
        $transactions = Transactions::where('user_id', $loggedInUser)
                                    ->orderBy('created_at','desc')
                                    ->get();

        return [
            'status' => true,
            'message' => 'All transactions',
            'data' => $transactions
        ];
    }

    public function searchTransactionByAmount($transactionAmount, $loggedInUser)
    {
        $transactions = Transactions::where('transaction_amount', 'like', '%'.$transactionAmount.'%')
            ->where('user_id', $loggedInUser)->get();

        return [
            'status' => true,
            'message' => 'Transaction search results',
            'data' => $transactions
        ];
    }
}
