<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Model\CartModel;
use App\Model\GoodsModel;
use Illuminate\Support\Str;

use App\Model\OrderModel;
use App\Model\OrderGoodsModel;


class Order extends Controller
{   
    /**
     * Undocumented function
     *  订单视图
     * @return void
     */
    public function index (){

        //获取后购物车中的东西
        $user_id = session('user.user_id');
        $cartInfo = CartModel::where(['user_id'=>$user_id])->get();
        //判断订单里的物品是否存在
        if(empty($cartInfo)){
            return redirect ('/home');
        }
        $cartInfo_arr = $cartInfo->toArray();
        // dd($cartInfo_arr);
        
        //查询下单的商品  详情    总价格
        $priceAll = 0;
        foreach($cartInfo_arr as $k=>$v){

            //查询商品的  新价格    在页面进行展示
            $goods = GoodsModel::find($v['goods_id']);
            $priceAll += $goods->shop_price;            //总金额
            $v['goods_price'] = $goods->shop_price;
            $v['goods_name']  = $goods->goods_name;
            $order_goods[] = $v;            //把查询到的数据 塞入到一个数组里
        }
        // dd($order_goods);
        //在订单表添加新数据
        $order_data = [
            'order_sn'=>strtolower(Str::random(20)),
            'user_id'=>$user_id,
            'money_paid'=>$priceAll,          //总金额
            'add_time'  =>time(),
        ];

        //获取自增ID
        $oid = OrderModel::insertGetId($order_data);

        //记录订单商品表
        foreach($order_goods as $k=>$v){
            $goods = [
                'order_id'=>$oid,
                'goods_id'=>$v['goods_id'],
                'goods_name'=>$v['goods_name'],
                'goods_price'=>$v['goods_price'],
            ];

            OrderGoodsModel::insertGetId($goods);
        }


        // print_r($order_goods);

        
        //跳转支付页面
        
        return view('Index/order',['order_goods'=>$order_goods,'priceAll'=>$priceAll,'oid'=>$oid]);

    }

    


}
