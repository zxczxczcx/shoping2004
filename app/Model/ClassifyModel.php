<?php

namespace App\Model;

use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;

class ClassifyModel extends Model
{
    //
    protected $table = 'p_category';
    public $timestamps = false;
    protected $primaryKey = 'cat_id';
    protected $guarded = [];   //黑名单  create只需要开启
    
    use ModelTree;  //开启  树
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setParentColumn('parent_id');        
        $this->setOrderColumn('cat_id');         //排序
        $this->setTitleColumn('cat_name');
    }

}
