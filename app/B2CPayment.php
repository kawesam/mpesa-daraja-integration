<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class B2CPayment extends Model
{
    //
    protected $table = 'b2c_payments';

    protected $fillable =['transactionId','resultcode','ResultDesc','amount','phone','receiver_names','TransactionCompletedDateTime','B2CUtilityAccountAvailableFunds','B2CWorkingAccountAvailableFunds','transaction_status'];

}
