<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GitUserModel extends Model
{
    
    protected $table = 'Github_User';
    public $timestamps = false;
    protected $primaryKey = 'uid';
    protected $guarded = [];   //黑名单  create只需要开启
}
