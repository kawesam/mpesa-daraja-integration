<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateC2bPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c2b_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('TransactionType');
            $table->string('transactionId');
            $table->integer('amount');
            $table->integer('businesscode');
            $table->string('billrefnumber');
            $table->float('organization_float');
            $table->string('ThirdPartyTransID')->nullable();
            $table->string('phone');
            $table->string('firstname')->nullable();
            $table->string('middlename')->nullable();
            $table->string('lastname')->nullable();
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
        Schema::dropIfExists('c2b_payments');
    }
}
