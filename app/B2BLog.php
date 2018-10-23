<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class B2BLog extends Model
{
    //
    protected $table = 'b2b_logs';
    protected $fillable = ['content'];
}
