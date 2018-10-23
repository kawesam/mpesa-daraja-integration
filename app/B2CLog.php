<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class B2CLog extends Model
{
    //
    protected $table = 'b2c_logs';
    protected $fillable = ['content'];
}
