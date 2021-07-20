<?php

namespace App\Http\Controllers;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,TransactionService $transactionService)
    {
        $loggedInUser = $request->user()->id;

        $transactionAmount = $request->input('amount');

        if($transactionAmount){

            return $transactionService->searchTransactionByAmount($transactionAmount, $loggedInUser);

        } else {

            return $transactionService->getTransactionsForLoggedInUser($loggedInUser);

        }
        

    }

}
