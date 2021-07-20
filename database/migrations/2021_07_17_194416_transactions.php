<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Transactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('transactions', function(Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('transaction_type');
            $table->string('account_balance');
            $table->integer('transaction_amount');
            $table->string('transaction_status');
            $table->string('transaction_reference');
            $table->string('transfer_code');
            $table->string('reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('transactions');
    }
}
