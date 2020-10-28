<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    
    protected $table = 'p_order';
    public $timestamps = false;
    protected $primaryKey = 'order_id';
    protected $guarded = [];   //黑名单  create只需要开启
}
