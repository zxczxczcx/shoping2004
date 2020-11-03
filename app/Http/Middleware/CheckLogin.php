<?php

namespace App\Http\Middleware;

use Closure;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {   
        //验证session
        $UserSession = session()->get('user.user_id');
        // dd($UserSession);
        if(empty($UserSession)){
            
            $response = [
                'error'=>400003,
                'msg'=>'请先登录'
            ];
            die(json_encode($response));
            return redirect('/login');

        }
        return $next($request);
    }
}
