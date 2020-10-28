<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //
    protected $table = 'p_users';
    public $timestamps = false;
    protected $primaryKey = 'user_id';
    protected $guarded = [];   //黑名单  create只需要开启
}
