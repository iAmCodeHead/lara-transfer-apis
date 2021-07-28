<?php


namespace App\Services;


use App\Models\AccountDetails;

class AccountService
{
    public function createAccount($fields, $loggedInUser)
    {
        $account = AccountDetails::create([
            'account_name' => $fields['account_name'],
            'account_number' => $fields['account_number'],
            'bank_name' => $fields['bank_name'],
            'account_balance' => 1000000,
            'user_id' => $loggedInUser->id
        ]);

        return  $account;

    }
}
