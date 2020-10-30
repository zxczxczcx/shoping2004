<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\IntegralModel;
use Encore\Admin\Grid\Filter\Day;

class Home extends Controller
{
    public function index(){

        return view ('home/indexs');
    }


    //签到
    public function sign(){
        $uid = session('user.user_id');         //本方法唯一的条件


        //判断用户今天是否签到

        $time = strtotime(date('Y-m-d'));       //当天凌晨
        $add_time = IntegralModel::where('user_id',$uid)->first('add_time');
        $add = date('Y-m-d',$add_time['add_time']);
        // dd(date('Y-m-d',time()));
        if($add==date('Y-m-d',$time)){
            $data = [
                'error'=>0,
                'msg'=>'今天已签过'
            ];
            return $data;
        }


        //拼凑数据
        $data = [
            'user_id'=>$uid,
            'add_time'=>time(),
            'integral'=>10,
            'day'=>1,
        ];
        $res = IntegralModel::insert($data);
        if($res){
            $data = [
                'error'=>0,
                'msg'=>'签到成功'
            ];
            return $data;
        }
        
        

        

    }
}
