<?php


namespace App\Services;


use App\Models\Transactions;
use Illuminate\Support\Facades\Http;

class TransferService
{

    protected $client;

    public function __construct()
    {
        $headers = [
            'Authorization' => "Bearer " . env('PAYSTACK_SECRET'),
            'Content-Type' => 'application/json',
        ];

        $this->client = Http::baseUrl('https://api.paystack.co')->withHeaders($headers);
    }

    public function initiate($user, $accountNumber, $bankCode, $amount, $reason)
    {

        try {

            $hasSufficientFunds = $this->checkAccountBalance($user, $amount);

            if(!$hasSufficientFunds){
                return [
                    'status' => false,
                    'message' => 'Insufficient funds',
                    'data' => []
                ];
            }

            $response = $this->validateAccountNumber($accountNumber, $bankCode);

            if ($response->status == true) {

                $transferRecipient = $this->createPaystackTransferRecipient([
                    'bank_code' => $bankCode,
                    'account_number' => $response->data->account_number,
                    'account_name' => $response->data->account_name
                ]);

                $transfer = $this->makePaystackTransfer($transferRecipient, $amount, $reason);


                $newAccountDetails = $this->updateTransaction($user, $transfer, $amount, $reason);

                return [
                    'status' => true,
                    'message' => 'Transfer successful',
                    'data' => $newAccountDetails
                ];

            }

            return ['status' => false, 'message' => 'Could not resolve account details. Please try again.'];

        } catch (\Exception $e) {

            return ['message' => 'Failed to complete transfer request. Please try again.'];

        }

    }

    private function checkAccountBalance($user, $amount)
    {
        $currentBalance = $user->account->account_balance;

        if($amount > $currentBalance){
            return false;
        }

        return true;
    }

    private function validateAccountNumber($accountNumber, $bankCode)
    {
        $response = $this->client->get('/bank/resolve?account_number=' . $accountNumber . '&bank_code=' . $bankCode);

        $body = $response->object();

        return $body;
    }

    private function createPaystackTransferRecipient($validateAccountDetails)
    {
        $response = $this->client->post('/transferrecipient', [
            "type" => "nuban",
            "name" => $validateAccountDetails['account_name'],
            "account_number" => $validateAccountDetails['account_number'],
            "bank_code" => $validateAccountDetails['bank_code'],
            "currency" => "NGN"
        ]);

        $body = $response->object();

        return $body->data;
    }

    private function makePaystackTransfer($recipient, $amount, $reason)
    {
        $response = $this->client->post('/transfer', [
            "source" => "balance",
            "amount" => ($amount * 100),  //paystack charges in kobo, multiply by 100 to get exact value in naira
            "recipient" => $recipient->recipient_code,
            "reason" => $reason
        ]);

        $body = $response->object();

        return $body->data;
    }

    private function updateTransaction($user, $transfer, $amount, $reason)
    {
        $currentBalance = $user->account->account_balance;

        Transactions::create([
            'user_id' => $user->id,
            'transaction_type' => 'transfer',
            'transaction_amount' => $amount,
            'transaction_status' => $transfer->status,
            'transfer_code' => $transfer->transfer_code,
            'reason' => $reason,
            'transaction_reference' => $transfer->reference,
            'account_balance' => $newBalance = ($currentBalance - $amount)
        ]);

        return $user->account()->update(['account_balance' => $newBalance]);
    }

    public function getBankCodes()
    {
            $response = $this->client->get('/bank?country=nigeria');

            $body = $response->object();

            return $body->data;

    }
}
