<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateB2bPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('b2b_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('resulttype');
            $table->integer('resultcode');
            $table->string('transactionId')->nullable();
            $table->text('ResultDesc');
            $table->float('InitiatorAccountCurrentBalance')->nullable();
            $table->float('DebitAccountCurrentBalance')->nullable();
            $table->float('DebitPartyAffectedAccountBalance')->nullable();
            $table->float('amount')->nullable()->nullable();
            $table->float('DebitPartyCharges')->nullable();
            $table->string('receiverName')->nullable();
            $table->integer('transaction_status');
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
        Schema::dropIfExists('b2b_payments');
    }
}
