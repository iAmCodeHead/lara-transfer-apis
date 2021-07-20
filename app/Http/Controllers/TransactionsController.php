<?php

namespace App\Http\Controllers;
use App\Services\TransactionsService;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,TransactionsService $transactionsService)
    {
        $loggedInUser = $request->user()->id;

        $transactionAmount = $request->input('amount');
        
        $transactions = $transactionsService->getTransactionsForLoggedInUser($loggedInUser, $transactionAmount);
       
        return response()->json($transactions, $transactions['statusCode']);

    }

}
