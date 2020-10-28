<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderGoodsModel extends Model
{
    //
    protected $table = 'p_order_goods';
    public $timestamps = false;
    protected $primaryKey = '';
    protected $guarded = [];   //黑名单  create只需要开启
}
