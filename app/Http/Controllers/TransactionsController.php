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

        $from = $request->input('from');
        
        $to = $request->input('to');

        $transactions = $transactionsService->getTransactionsForLoggedInUser($loggedInUser, $from, $to);
       
        return response()->json(['status' => true, 'message' => 'Fetch transactions','data' => $transactions]);

    }

}
