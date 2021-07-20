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

        $fields = $request->validate([
            'account_name' => 'required|string',
            'account_number' => 'required|digits:10',
            'bank_name' => 'required|string',
            'account_balance' => 'nullable|integer'
        ]);

        $newUser = $accountService->createAccount($fields, $loggedInUser);

        return response()->json($newUser, 200);
    }

}
