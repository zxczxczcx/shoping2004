<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RandModel extends Model
{
    protected $table = 'p_rand_shop';
    protected $primaryKey = 'rand_id';
    public $timestamps = false;
    protected $guarded = [];   //黑名单  create只需要开启
}
