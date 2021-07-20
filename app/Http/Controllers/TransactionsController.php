<?php

namespace App\Http\Controllers;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(TransactionService $transactionService)
    {
        $loggedInUser = $this->request->user()->id;

        return $transactionService->getTransactionsForLoggedInUser($loggedInUser);

    }


    /**
     * search for specified resource from storage.
     *
     * @param  date  $date
     * @return \Illuminate\Http\Response
     */
    public function search($transactionAmount, TransactionService $transactionService)
    {
        $loggedInUser = $this->request->user()->id;

        return $transactionService->searchTransactionByAmount($transactionAmount, $loggedInUser);
    }
}
