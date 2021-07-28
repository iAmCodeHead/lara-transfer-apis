<?php

namespace App\Http\Controllers;

use App\Services\AccountService;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, AccountService $accountService)
    {
        $loggedInUser = $request->user();

        $request->validate([
            'account_name' => 'required|string',
            'account_number' => 'required|digits:10|unique:account_details',
            'bank_name' => 'required|string',
            'account_balance' => 'nullable|integer'
        ]);

        $newUser = $accountService->createAccount($request, $loggedInUser);

        return response()->json(['status' => true, 'message' => 'Account creation successful','data' => $newUser]);
    }

}
