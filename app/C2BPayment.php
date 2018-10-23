<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class C2BPayment extends Model
{
    //
    protected $table = 'c2b_payments';

    protected $fillable = ['TransactionType','transactionId','amount','businesscode','billrefnumber','organization_float','organization_float','ThirdPartyTransID','phone','firstname','middlename','lastname'];
}
