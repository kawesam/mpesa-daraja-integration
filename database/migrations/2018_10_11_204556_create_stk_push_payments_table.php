<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStkPushPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stkPush_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('MpesaReceiptNumber')->nullable();
            $table->string('phone')->nullable();
            $table->string('amount')->nullable();
            $table->integer('ResultCode');
            $table->text('ResultDesc');
            $table->integer('status');
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
        Schema::dropIfExists('stkPush_payments');
    }
}
