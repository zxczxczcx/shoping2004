<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Model\OrderModel;

use App\Model\CartModel;
class PayController extends Controller
{
    /**
     * Undocumented function
     *  下订单    并跳转 支付页面
     * @return void
     */
    public function alibuy(Request $request){
        $oid = $request->get('oid');        //接受订单id
        
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
        // dd($paramtwo );

        //公共参数
        $paramthree = [
            'app_id'    =>env('ALIPAY_APP_ID'),
            'method'    =>'alipay.trade.page.pay',
            'return_url'=>env('ALIPAY_RETURN_URL'),                     //同步跳转地址
            'charset'   =>'utf-8',
            'sign_type' =>'RSA2',
            'sign'      =>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApnneG5GShwcqnxPgol4jRyNSnI2RJsYm/4MMb8gV7+IgjiJM2c8nc4yZWbYuc2JdzMxEf36uw7uWvb7AzEX43eK/I73DyKWS5b2mEAYsEDIb3Ok7Kd41lnmzA0doyC/+JhyluVFc64ns5lE7u8zuGWkE+ZMzPsKKbddvxXtVUeVpWdUcui+LJ64F/cV08rLDOV+DUPMBx1XeyP1CWvnlfDbn1JLiFkk+KNHlltMiK3FmleQ2mvVeMwr9YZewkcp37s2mG3gQNIjkApa4oUoPHv+TfLiyLa+kU/TOqjJB/VC+9GKcSQ0YhoWyo3nNNbFiwX/0xis7qqkQzye7qgwIDAQAB',
            'timestamp'     => date('Y-m-d H:i:s'),
            'version'   =>'1.0',
            'notify_url'=>env('ALIPAY_NOTIFY_URL'),    //异步跳转
            'biz_content'   => json_encode($paramtwo),
        ];


        // $url = 'https://openapi.alipaydev.com/gateway.do';
        ksort($paramthree);
        
        $str = "";
        foreach($paramthree as $k=>$v)
        {
            $str .= $k . '=' . $v . '&';
        }
        $str = rtrim($str,'&');     // 拼接待签名的字符串
        $sign = $this->aliSign($str);
        // dd($str);die;
        //沙箱测试地址
        $url = 'https://openapi.alipaydev.com/gateway.do?'.$str.'&sign='.urlencode($sign);
        
        return redirect($url);


    }

    public function aliSign($data){
        // dd($data);
        $priKey = file_get_contents(storage_path('keys/priv.key'));
        // dd($priKey);
        $res =  openssl_pkey_get_private($priKey);

        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

        openssl_sign($data,$sign,$res,OPENSSL_ALGO_SHA256);
        openssl_free_key($res);
        $sign = base64_encode($sign);
        return $sign;
    }
}
