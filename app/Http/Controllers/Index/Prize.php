<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\PrizeModel;


class Prize extends Controller
{   
    /**
     * Undocumented function
     *  抽奖视图
     * @return void
     */
    public function index (){

        return view ('Index/prize');
    }
    
    /**
     * Undocumented function
     *  执行抽奖
     * @return void
     */
    public function start(){
        //判断用户是否登录
        $user_id = session('user.user_id');
        if(empty($user_id)){
            $data = [
                'error'=>400001,
                'msg'=>'用户未登录，请先登录',
            ];
            return $data;
        }

        //判断当前用户是否已经抽过
        $time = strtotime(date('Y-m-d'));       //凌晨
        // echo $time ;die;

        $prizeInfo = PrizeModel::where(['user_id'=>$user_id])->where('add_time','>=',$time)->first();
        if($prizeInfo){
            $data = [
                'error'=>400002,
                'msg'=>'今天抽奖次数已用完，请改日再试',
            ];
            return $data;
        }

        // 该用户是否已中奖      未完成
        // $prize_level = PrizeModel::where(['user_id','=',$user_id])->get();

        // print_R($prize_level);die;
        // if($prize_level->prize_level>0){
        //     $data = [
        //         'error'=>0,
        //         'msg'=>'谢谢惠顾',
        //     ];
        //     return $data;
        // }

        //中奖概率
        $rand =  mt_rand(0,100000);
        $level = 0;
        if($rand>=1&&$rand<=10){
            $level = 1;
        }else if($rand>=11&&$rand<=30){
            $level = 2;
        }else if($rand>=31&&$rand<=60){
            $level = 3;
        }

        //建表入库
        $prize_data = [
            'user_id'=>$user_id,
            'prize_level'=>$level,
            'add_time'=>time()
        ];
        $res = PrizeModel::insertGetId($prize_data);
        if($res){
            $data = [
                'error'=>0,
                'msg'=>'ok',
                'data'=>[
                    'rand'=>$rand,
                    'level'=>$level
                ]
            ];
        }else{
            $data = [
                'error'=>500008,
                ''=>'网络繁忙，请稍后再试'
            ];
        }
        return $data;

    }


}
