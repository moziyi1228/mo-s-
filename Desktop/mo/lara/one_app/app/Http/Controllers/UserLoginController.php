<?php

namespace App\Http\Controllers;
use App\Models\UsersModel;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;


class UserLoginController extends Controller
{


    //后台用户登陆
    public function UserLogin(Request $request){
       //获取表单提交数据
        $userinfo=$request->all();

        //实例化UsersModel
        $Users=new UsersModel;

        //数据库获取信息
          $userinfo = $Users -> UsersModelSelect($userinfo);

        if(!$userinfo){
            return false;
        }
//        生成token值保存在userinfo
         $token = time().'one'.rand(100000,999999);
         $userinfo['token']=$token;
//      更新session  sesion保存登陆信息和token令牌
         $request->session()->forget('user');
          session()->push('user',$userinfo);
        // 通过全局 Session 助手函数存储 ...
          session()->save();
        //写入redis
        if ($this->PutInRedis($userinfo,'user:'.$userinfo['userid'],600)===false){
            //      更新session  sesion保存登陆信息和token令牌
            $request->session()->forget('user');
            return "服务器故障！";
        }
        return $userinfo;
    }

    public function UserLogout(Request $request){
//        删除session
         $request->session()->forget('user');

         if(Redis::del('user:'.$request['userid'])>0){
             return redirect("login");
         }
        return redirect("login");
    }


    //写入redis设置过期时间
    public function PutInRedis($info,$key,$time){
        if(Redis::setnx($key,$info)!==0){
            return Redis::expire($key,$time );
        }
        return false;

    }


//    微信用户登陆
    public function WxLogin(Request $request){
//        获取code值
        $code = implode($request->all('code'));
        $user=new UsersModel();
        $wxinfo = $user ->GetOpenid($code);

        if(isset($wxinfo['errcode'])){
           return response()->json($wxinfo);
        }
//       用户登陆,把用户信息传进去
        $res = $user->WxLogin(json_decode($request['rawData'],true),$wxinfo);
        return  response()->json($res);
    }


//    获取用户手机号
    public function GetPhoneNum(Request $request){
        //        获取code值
        $code = implode($request->all('code'));


        $tokenResult = file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('APPID').'&secret='.env('APPSECRET'));
        $access_token = json_decode($tokenResult,true)['access_token'];
//        POST https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=ACCESS_TOKEN
        $url = 'https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token='.$access_token;
        $query=[
            'code'=>$code
        ];
        $param = json_encode($query);
        $headers = array("Content-type: application/json;charset=UTF-8","Accept: application/json","Cache-Control: no-cache", "Pragma: no-cache");
        //curl模拟post请求
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        $result = json_decode($data,true);
        if(!is_array($result)){
           $res= ['errmsg'=>'微信接口返回空'];
        }
        if($result['errmsg']!=='ok'){
            $res= ['errmsg'=>'微信接口返回错误'];
        }else{
            //        用户更新信息
            $userinfo = array( 'user_tel'=>$result['phone_info']['phoneNumber'],'updated_at'=>Date('Y-m-d H:m:s'));
            $update =  $this->UpdateUser($userinfo,$request['user_id']);
            if($update){
//                返回用户号码
                $res= ['errmsg'=>'ok','phoneNumber'=>$result['phone_info']['phoneNumber']];

            }else{

                $res= ['errmsg'=>'更新失败'];
            }

        }

        return response()->json($res);
    }

//    更新用户信息
    public function UpdateUser($userinfo,$userid){
        //      把用户信息传进去
        $user = new UsersModel();
         return $user->UpdateUser($userinfo,$userid);


    }


}
