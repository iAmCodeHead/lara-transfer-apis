<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {

            $table->renameColumn('transaction_type','type');
            $table->renameColumn('transaction_amount','amount');
            $table->renameColumn('transaction_status','status');
            $table->renameColumn('transaction_reference','reference');

            $table->dropColumn('transfer_code');
            
        });

         Schema::table('transactions', function (Blueprint $table) {

            $table->integer('account_balance')->change();
            $table->string('reference')->nullable()->change();
            $table->string('reason')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {

            $table->renameColumn('type', 'transaction_type');
            $table->string('account_balance')->change();
            $table->renameColumn('amount','transaction_amount');
            $table->renameColumn('status','transaction_status');
            $table->renameColumn('reference','transaction_reference');
            $table->string('reference')->nullable(false)->change();
            $table->string('reason')->nullable(false)->change();
            $table->string('transfer_code');

        });
    }
}
