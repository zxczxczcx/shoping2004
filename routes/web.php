<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});




Route::prefix('/')->group(function(){
    Route::get('register','Index\Login@register'); //注册
    Route::post('registerDo','Index\Login@registerDo'); //执行注册
    Route::get('sendEmail','Index\Login@sendEmail');//发送 邮箱

    Route::get('login','Index\Login@login'); //前台登录
    Route::post('loginDo','Index\Login@loginDo');//执行登录 

    Route::get('quit','Index\Login@quit');//退出

    Route::get('/','Index\Index@index');    //首页

    Route::get('item','Index\Cart@item');//商品详情页
    Route::get('rand','Index\Cart@rand');//商品评论 


    Route::get('add','Index\Cart@add');//加入购物车
    Route::get('fav','Index\Cart@favone');//收藏
    Route::get('favtwo','Index\Cart@favtwo');//取消收藏

    Route::get('cart','Index\Cart@cart');//购物车视图
    Route::get('order','Index\Order@index');//下订单
    Route::get('alibuy','Index\Order@alibuy');//支付

    Route::get('githublogin','Index\Login@githublogin');//github   第三方登录


    Route::get('prize','Index\Prize@index');//抽奖
    Route::get('start','Index\Prize@start');//抽奖处理

    
    //个人中心
    Route::get('hindex','Home\Home@index');//个人首页
    Route::get('sign','Home\Home@sign');//签到


    




    


    //订单




});



