<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class IntegralModel extends Model
{
    //签到表
    protected $table = 'p_integral';
    // protected $primaryKey = '';      默认id
    public $timestamps = false;
    
}
