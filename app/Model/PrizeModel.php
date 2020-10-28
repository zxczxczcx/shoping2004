<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PrizeModel extends Model
{
    //设置数据库
    protected $table = 'p_prize';
    public $timestamps = false;
    protected $guarded = [];   //黑名单  create只需要开启
    
    
}
