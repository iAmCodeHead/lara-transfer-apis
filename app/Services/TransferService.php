<?php


namespace App\Services;


use App\Models\Transactions;
use Exception;
use Illuminate\Support\Facades\Http;
use UnexpectedValueException;


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

            $this->checkAccountBalance($user, $amount);

            $response = $this->validateAccountNumber($accountNumber, $bankCode);

            $newTransaction = $this->debitWallet($user, $amount, $reason);

            $transferData = [
                'bank_code' => $bankCode,
                'account_number' => $response->data->account_number,
                'account_name' => $response->data->account_name
            ];

            $transferRecipient = $this->createPaystackTransferRecipient($transferData);

            try {

               $transfer = $this->makePaystackTransfer($transferRecipient, $amount, $reason);

               $newTransaction->update([
                   'status' => 'success',
                   'reference' => $transfer->reference
                ]);
               
            } catch (\Throwable $th) {
                
                // save failed job for retry

            }

            
            return $newTransaction;


    }

    private function checkAccountBalance($user, $amount)
    {
        
        $currentBalance = $user->account->account_balance;

        if($amount > $currentBalance){

            throw new UnexpectedValueException('Insffucient Funds');

        }

        return true;
    }

    private function debitWallet($user, $amount, $reason)
    {
        $currentBalance = $user->account->account_balance;

        $newTransaction = Transactions::create([
            'user_id' => $user->id,
            'type' => 'debit',
            'amount' => $amount,
            'status' => 'pending',
            'reason' => $reason,
            'account_balance' => $newBalance = ($currentBalance - $amount)
        ]);

        $user->account()->update(['account_balance' => $newBalance]);
        
        return $newTransaction;

    }

    private function validateAccountNumber($accountNumber, $bankCode)
    {
        $response = $this->client->get('/bank/resolve?account_number=' . $accountNumber . '&bank_code=' . $bankCode);

        $body = $response->object();

        if ($body->status == false) {
        
            throw new UnexpectedValueException('Could not resolve account details. Please try again');
        
        }

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

        if ($body->status == false) {
            
            throw new Exception('Failed to create recipient. Please try again');
        
        }

        return $body->data;
    }

    private function makePaystackTransfer($recipient, $amount, $reason)
    {
        $response = $this->client->post('/transfer', [
            "source" => "balance",
            "amount" => $amount,
            "recipient" => $recipient->recipient_code,
            "reason" => $reason
        ]);

        $body = $response->object();

        if ($body->status == false) {
            
            throw new Exception('Failed to Make transfer. Please try again');
            
        }

        return $body->data;
    }

    public function getBankCodes()
    {
            $response = $this->client->get('/bank?country=nigeria');

            $body = $response->object();

            if ($body->status == false) {
                
                throw new Exception('Failed to get banks. Please try again');
            
            }

            return  $body->data;

    }
}
