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
            'amount' => 'required|integer',
            'reason' => 'nullable|string'
        ]);

        $transferResponse = $transferService->initiate(
            $loggedInUser,
            $fields['account_number'],
            $fields['bank_code'], $fields['amount'],
            $fields['reason']
        );

        return response()->json($transferResponse, 200);

    }

    public function getBanks(TransferService $transferService)
    {
       $bankCodes = $transferService->getBankCodes();

       return response()->json($bankCodes, $bankCodes['statusCode']);

    }
}
