<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Model\index\UserModel;

// use Mail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

use GuzzleHttp\Client;

use App\Model\GitUserModel;

class Login extends Controller
{
    /**
     * 
     * 登录
     * 
     */
    public function login (){
        //如果session中有值  ，这跳转个人中心
        $user_id = session('user.user_id');
        if($user_id){
            return redirect('hindex');
        }

        return view ('Index/login');
    }

    //执行登录
    public function loginDo (Request $request){
        
        $account = $request->post('account');
        $pwd = $request->post('pwd');
        
        if(!empty($account)){
            //该账号的信息
            $account = UserModel::where('user_name',$account)->orwhere('user_tel',$account)->orwhere('user_email',$account)->first();   
            //此处定义一个唯一的值
            $key = 'user:id:'.$account->user_id;

            // dd($key);
            //提取错误次数
            $error_number = Redis::get($key);
            
            if($error_number>=5){
                $time = Redis::expire($key,60*60);
                $time = ceil(60-($time/60));
                return redirect('Index/login')->with('msg','密码错误次数太多，请分钟');
            }

        
            // dd($error_number);
            if(!empty($account)){
                if($pwd==$account->user_pwd){
                    //成功
                    Redis::del($key);
                    $user = ['user_id'=>$account['user_id'],'user_name'=>$account['user_name']];
                    session(['user'=>$user]);
                    return redirect ('/');
                }else{
                    $number = Redis::incr($key);
                    if($number>=1){
                        Redis::expire($key,600);
                        return redirect('Index/login')->with('msg','密码错误'.$number.'次');
                    }
                    // dd($error_number);

                    return redirect('Index/login')->with('密码输入错误，请重新输入');

                }

                
            }else{
                return redirect ('Index/login')->with('msg','请先注册');
            }



        }else{
            return redirect('Index/login')->with('msg','账号不能为空');
        }

    }

    //注册
    public function register (){

        return view ('Index/register');
    }

    /**执行注册 */
    public function registerDo (Request $request){
        
        $password = $request->post('password');
        $data = [
            'user_name' => $request->post('user_name'),
            'user_pwd' => $request->post('user_pwd'),
            'user_tel' => $request->post('user_tel'),
            'user_email' => $request->post('user_email')
        ];

        if($data['user_pwd']!=$password){
            return redirect('Index/register')->with('pwd','确认密码和登录密码不一致');die;
        }

        
        $data['register_time']=time();

        $uid = UserModel::insertGetId($data);       //获取当前添加的id
        // dd($uid);

        //发送邮件  认证信息  生成激活码链接
        $active_code = Str::random(64);     //生成一个唯一字符串 
        //保存激活码与用户的对应关系   使用有序结合
        $redis_active_key = 'ss:user:active';
        Redis::zAdd($redis_active_key,$uid,$active_code); 
        
        $active_url = env('APP_URL').'/user/active:'.$active_code;
        // dd($active_url);


        $registerInfo = UserModel::insert($data);
        if($registerInfo){
            Redis::set('email',$data['user_email']);
            return redirect('/sendEmail');
        }else{
            echo '注册失败，请重新输入确认密码是否与登录密码保持一致';die;
        }
        
    }

    /***
     * 发送邮箱验证
     */
    public function sendEmail(){
        //获取邮箱
        $email = Redis::get('email');
        echo $email;
    }

    /**
     * 登录方式
     */
    public function github(){
        
    }

    /**
     * github登录
     */
    public function githublogin(Request $request){
        //接受code的值
        $code = $request->get('code');
        // echo 'code:'.$code;die;
            

        // //获取access_token
        // $this->getAccessToken($code);
        
        
        //接受code
        // $code = $_GET['code'];

        //换取 access_token
        $token = $this->getAccessToken($code);
        // dd($token);
        //获取用户信息
        $git_user = $this->getGithunUserInfo($token);
        // dd($git_user);
        //根据获取的id 查询数据库
        $user = GitUserModel::where(['guid'=>$git_user->id])->first();
        // dd();
            if($user){
                // 跳转登录     存在
                $this->weblogin($user);
            }else{
                //不存在

                
                $new_user = [
                    'user_name'=>Str::random(10)
                ];
                //把上一条随机的名字插入到 用户表 中  在去出自增id
                $user_id = UserModel::insertGetId($new_user);
                $info = [
                    'uid'       =>$user_id,
                    'guid'              =>$git_user->id,
                    'avatar'                =>  $git_user->avatar_url,
                    'github_url'            =>  $git_user->html_url,
                    'github_username'       =>  $git_user->name,
                    'github_email'          =>  $git_user->email,
                    'add_time'              =>  time()
                ];
                
                //需要一维数组
                $user = GitUserModel::insertGetId($info);       //插入新数据

                //  登录逻辑
            $this->weblogin($user);
            }

        return redirect('/home');       //登录成功 返回首页

    }

    /**
     * 
     * 获取access_token
     * 用户中心 登录状态  才可访问
     * 
     */
    protected function getAccessToken($code){

        $url = 'https://github.com/login/oauth/access_token';
        //GET 请求接口
        $client = new Client();
        $response = $client->request('POST',$url,[
            'verify'=>false,
            'form_params'=>[
                'client_id'     =>'45097703bfe86eac220a',
                'client_secret' =>'8874cd2d9145261064333d927a8d0aa48647c8f1',
                'code'          =>$code
            ]
        ]);

        parse_str($response->getBody(),$str);
        // dd($str);
        return $str['access_token'];
        //如果出现未定义的索引    ：重新点击登录


    }

    public function getGithunUserInfo($token){

        $url = 'https://api.github.com/user';
        //GET 请求接口      调用接口 Guzzle
        $client = new Client();
        
        $response = $client->request('GET',$url,[
            'verify'=> false,
            'headers'=>[
                'Authorization'=>"token $token"
                
            ]
        ]);
        // echo $response->getStatusCode();die;
        return json_decode($response->getBody());

    }

    /**
     * 存session
     */

    public function weblogin($user){
        session(['user'=>$user]);
    }

    /**退出 */
    public function quit(){
        request()->session()->forget('user');
        return redirect('/login');
        
    }

}
