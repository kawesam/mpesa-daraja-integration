<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SdkPushLog extends Model
{
    //
    protected $table = 'sdkpush_logs';
    protected $fillable = ['content'];
}
