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

    /**
     * Undocumented function
     *  下订单    并跳转 支付页面
     * @return void
     */
    public function alibuy(Request $request){
        $oid = $request->get('oid');
        //判断  改订单是否存在    或者已交易
        $orderInfo = OrderModel::where(['order_id'=>$oid])->first();
        if(empty($orderInfo)){
            echo '改订单不存在，请重新选择商品';die;
        }else{
            $orderInfo = $orderInfo->toArray();
        }
        // dd($orderInfo);

        //组合参数，调用接口，支付
        

        //请求参数
        $paramtwo = [
            'out_trade_no' => $oid,
            'product_code' =>'FAST_INSTANT_TRADE_PAY',
            'total_amount' =>$orderInfo['money_paid'],
            'subject'      =>'桃源测试--'.$orderInfo['order_sn'],
        ];

        //公共参数
        $paramthree = [
            'app_id'    =>env('ALIPAY_APP_ID'),
            'method'    =>'alipay.trade.page.pay',
            'return_url'=>env('ALIPAY_RETURN_URL'),                     //同步跳转地址
            'charset'   =>'utf-8',
            'sign_type' =>'RSA2',
            'sign'      =>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiJwtgSQHtaqsvw+to46lgf7QljQuaaIfhSY/Lid31Jme4BYuTCKRnGZe1vyX4Ow65+yOlsNS2ArUS5gjgJglxruSmbN279TBFhzzOwdFXXRrJFxpXF8iMH/Yceyx+rx24MYWWvC9aTZoObfII2QzgQXz9XleEyyU18trWWXdhscExboN3Qts6RbLC2Z0f0SxSRvhQcHVWErRfSGmrlJ+sQWznzGmGpMCVl8OO6vw7VdzNE7tTryJvKi1/CvzjnYhAcEMLJiqHsgS3dBTTYVBJej86qd/EVuazBVs9LPHAcHvAFcQiOk6uqn1gh37SeBsV1meqekHODmao3JOltDJlwIDAQAB',
            'timestamp'     => date('Y-m-d H:i:s'),
            'version'   =>'1.0',
            'notify_url'=>env('ALIPAY_NOTIFY_URL'),    //异步跳转
            'biz_content'   => json_encode($paramtwo),
        ];


        // $url = 'https://openapi.alipaydev.com/gateway.do?';
        ksort($paramthree);
        $str = "";
        foreach($paramthree as $k=>$v)
        {
            $str .= $k . '=' . $v . '&';
        }
        $str = rtrim($str,'&');     // 拼接待签名的字符串
        $sign = $this->aliSign($str);

        //沙箱测试地址
        $url = 'https://openapi.alipaydev.com/gateway.do?'.$str.'&sign='.urlencode($sign);
        return redirect($url);


    }

    public function aliSign($str){
        $priKey = file_get_contents(storage_path('keys/priv.key'));
        $res = openssl_get_privatekey($priKey);

        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

        openssl_sign($str, $sign, $res, OPENSSL_ALGO_SHA256);
        openssl_free_key($res);
        $sign = base64_encode($sign);
        return $sign;
    }


}
