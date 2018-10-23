<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class C2BLog extends Model
{
    //
    protected $table = 'c2b_logs';
    protected $fillable = ['content'];
}
