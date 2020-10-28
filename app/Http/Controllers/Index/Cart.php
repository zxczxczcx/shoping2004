<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Model\GoodsModel;
use Illuminate\Support\Facades\Redis;
use App\Model\CartModel;
use App\Model\FavGoodsModel;
use App\Model\RandModel;

class Cart extends Controller
{
    
    /**
     * 商品详情
     */
    public function item(Request $request){
            $goods_id = $request->get('id');
            //存缓存

            $key = 'goods_id:'.$goods_id;
            
            //点击流量          
            



            $goods_Info = Redis::hgetAll($key);
            if(empty($goods_Info)){
                echo '没有缓存';
                $goods_Info = GoodsModel::find($goods_id);
                if(!empty($goods_Info)){
                    $goods_Info = $goods_Info->toArray();
                    //hset 存一个值    hMset可存多个值
                    //在设置过期

                    Redis::hMset($key,$goods_Info);     //
                }else{
                    echo '改商品已下架';die;
                }
                
            }else{
                echo '有缓存，不用查数据库';
            }
            // echo '<pre>';print_r(session('user'));echo '</pre>';

            $user_id = session('user.user_id');


            //查询该用户是否收藏
            $favInfo = FavGoodsModel::where(['user_id'=>$user_id])->get();
            $gid = [];
            foreach($favInfo as $k=>$v){
                $gid[] = $v['goods_id'];
            }



            //根据当前商品id 查询商品的评论
            $randInfo = RandModel::where(['goods_id'=>$goods_id])->get();
            $user_name = session('user.user_name');
            foreach($randInfo as $k=>$v){
                $v['user_name']=$user_name;
            }

            // dd($randInfo);
            
            
            // dd($user_name);

            return view('Index/item',['goods_Info'=>$goods_Info,'gid'=>$gid,'randInfo'=>$randInfo]);
        
    }

    /**加入购物车 */
    public function add(Request $request){
        $id = $request->get('id');      //商品id
        $user_id = session('user.user_id');  //用户id

        // dd($user_id);
        if(empty($user_id)){
            $error = [
                'error'=>400001,
                'hint'=>'请先登录'                  //提示
            ];
            return json_encode($error);
        }

        $data = [
            'goods_id'=>$id,
            'user_id'=>$user_id,
            'cart_time'=>time(),
        ];

        $res = CartModel::insert($data);
        if($res){
            $error = [
                'error'=>0,
                'hint'=>'加入成功'
            ];
        }else{
            $error = [
                'error'=>500001,
                'hint'=>'加入失败'
            ];
        }

        return json_encode($error);

    } 

    /**购物车 */
    public function cart(){
        
        //查询购物车的内容
        //根据用户ID查询当前用户的购物车
        $user_id = session('user.user_id');
        
        //  根据用户id查询当前用户购物车里的商品  
        $goodsId = CartModel::where(['user_id'=>$user_id])->get()->toArray();
        // dd($goodsId);
        //循环得到商品id
        $goodsInfo = [];
        foreach($goodsId as $k=>$v){
            $goodsInfo[] = GoodsModel::find($v['goods_id'])->toArray();
        }
        // dd($goodsInfo);

        
        
        return view('index/cart',['goodsInfo'=>$goodsInfo]);
    }

    /**收藏  js*/
    public function favone(Request $request){
        $goods_id = $request->get('id');
        $user = session()->get('user.user_id');
        if(empty($user)){
            $data = [
                'error'=>400001,
                'hint'=>'请先登录',
            ];
            return $data;
        }

        //拼接数据
        $data = [
            'user_id'=>$user,
            'goods_id'=>$goods_id,
            'add_time'=>time(),
        ];
        $favInfo = FavGoodsModel::insert($data);
        if($favInfo){
            $data = [
                'error'=>0,
                'hint'=>'收藏成功',
            ];
            return $data;
        }
    }

    /**取消收藏  js */
    public function favtwo(Request $request){
    
        $goods_id = $request->get('id');
        $user = session()->get('user.user_id');
        
        $favInfo = FavGoodsModel::where([['user_id','=',$user],['goods_id','=',$goods_id]])->delete();
        
        if($favInfo){
            $data = [
                'error'=>0,
                'hint'=>'成功取消',
            ];
            return $data;
        }
    }

    /**评论  js*/
    public function rand(Request $request){
        //获取
        $val = $request->get('val');
        $goods_id = $request->get('goods_id');
        $user = session()->get('user.user_id');
        if($user){
            if(!empty($val)){
                $data = [
                    'rand_title'=>$val,
                    'goods_id'=>$goods_id,
                    'user_id'=>$user,
                    'add_time'=>time()
                ];
                $res = RandModel::insert($data);
                if($res){
                    $info=[
                        'error'=>0,
                        'hint'=>'发布成功'
                    ];
                    return $info;
                }
            }
            
        }else{
            //未检测到用户登录
            $info=[
                'error'=>400001,
                'hint'=>'请先登录'
            ];

            return $info;
        }
    }



}
