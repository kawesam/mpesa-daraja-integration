<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateB2cPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('b2c_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transactionId');
            $table->integer('resultcode');
            $table->text('ResultDesc');
            $table->float('amount')->nullable();
            $table->string('phone')->nullable();
            $table->string('receiver_names')->nullable();
            $table->float('B2CUtilityAccountAvailableFunds')->nullable();
            $table->float('B2CWorkingAccountAvailableFunds')->nullable();
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
        Schema::dropIfExists('b2c_payments');
    }
}
