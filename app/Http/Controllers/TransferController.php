<?php

namespace App\Http\Controllers;

use App\Services\TransferService;
use Illuminate\Http\Request;

class TransferController extends Controller
{

    public function initiate(Request $request, TransferService $transferService)
    {
        $loggedInUser = $request->user();

        $fields = $request->validate([
            'account_number' => 'required|digits:10',
            'bank_code' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'reason' => 'nullable|string'
        ]);

        $transaction = $transferService->initiate(
            $loggedInUser,
            $fields['account_number'],
            $fields['bank_code'], $fields['amount'],
            $fields['reason']
        );

        if($transaction->status == 'success'){

            return response()->json(['status' => true, 'message' => 'Transfer successful']);

        }

        return response()->json(['status' => true, 'message' => 'Transfer queued']);

    }

    public function getBanks(TransferService $transferService)
    {
       $bankCodes = $transferService->getBankCodes();

       return response()->json(['status' => true, 'message' => 'Fetch all banks','data' => $bankCodes]);

    }
}
