<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FavGoodsModel extends Model
{
    //
    protected $table = 'p_fav_goods';
    public $timestamps = false;
    // protected $primaryKey = 'uid';
    protected $guarded = [];   //黑名单  create只需要开启
}
