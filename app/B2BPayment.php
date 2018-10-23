<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class B2BPayment extends Model
{
    //
    protected $table = 'b2b_payments';

    protected $fillable =['resulttype','resultcode','transactionId','ResultDesc','InitiatorAccountCurrentBalance','DebitAccountCurrentBalance','DebitPartyAffectedAccountBalance','amount','DebitPartyCharges','receiverName','transaction_status'];


}
