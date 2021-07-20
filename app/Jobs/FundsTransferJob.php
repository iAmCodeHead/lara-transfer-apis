<?php

namespace App\Jobs;

use App\Models\AccountDetails;
use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\Transactions;

class FundsTransferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $validatedAccountDetails;
    private $loggedInUser;
    private $fields;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($validatedAccountDetails, $fields, $loggedInUser)
    {
        //
        $this->validatedAccountDetails = $validatedAccountDetails;
        $this->loggedInUser = $loggedInUser;
        $this->fields = $fields;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try{

            $response = Http::withHeaders(
                ['Authorization' => 'Bearer sk_test_24dbd3dce2556d05098b5c8d2560bb8ddf23283f'])
                ->post('https://api.paystack.co/transferrecipient',[
                    "type" => "nuban",
                    "name" => $this->validatedAccountDetails->{'data'}->{'account_name'},
                    "account_number" => strval($this->fields['account_number']),
                    "bank_code" => $this->fields['bank_code'],
                    "currency" => "NGN"
                ]);

            $transferRecipient = $response->object();

        }catch(\Exception $e) {
            $response = ['message'=>'There was an error creating transfer. Please try again.', 'error'=>$e];
            return response()->json($response, 500);
        }

        try{

            $response = Http::withHeaders(
                ['Authorization' => 'Bearer sk_test_24dbd3dce2556d05098b5c8d2560bb8ddf23283f'])
                ->post('https://api.paystack.co/transfer',[
                    "source" => "balance",
                    "amount" => ($this->fields['amount'] * 100), //paystack charges in kobo, multiply by 100 to get exact value in naira
                    "name" => $this->validatedAccountDetails->{'data'}->{'account_name'},
                    "recipient" => $transferRecipient->{'data'}->{'recipient_code'},
                    "reason" => $this->fields['reason'] || 'we rise by lifting others'
                ]);

            $successfulTransfer = $response->object();

        }catch(\Exception $e) {
            $response = ['message'=>'There was an error making transfer. Please try again.'];
            return response()->json($response, 500);
        }

        try {
            $accountDetails = AccountDetails::where('user_id',$this->loggedInUser->id)->first();

            $currentBalance = $accountDetails['account_balance'];
            $amountTransferred = $this->fields['amount'];

            $newBalance = ($currentBalance - $amountTransferred);

            Transactions::create([
                'user_id' => $this->loggedInUser->id,
                'transaction_type' => 'transfer',
                'transaction_amount' => $amountTransferred,
                'transaction_status' => $successfulTransfer->{'data'}->{'status'},
                'transfer_code' => $successfulTransfer->{'data'}->{'transfer_code'},
                'reason' => $this->fields['reason'],
                'transaction_reference' => $successfulTransfer->{'data'}->{'reference'},
                'account_balance' => $newBalance
            ]);

            $newAccountDetails = $accountDetails->update(['balance' => $newBalance]);

            $response = [
                'message' => 'Transfer successful',
                'data' => $newAccountDetails
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
