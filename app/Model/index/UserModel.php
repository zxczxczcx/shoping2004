<?php

namespace App\Model\index;

use Illuminate\Database\Eloquent\Model;


class UserModel extends Model
{
    //
    protected $table = 'user';              //表明
    public $timestamps = false;
    protected $primaryKey = 'user_id';      //自增id
    protected $guarded = [];   //黑名单  create只需要开启



}
