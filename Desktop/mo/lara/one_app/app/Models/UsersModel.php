<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersModel extends Model
{
    use HasFactory;
    //必须
    protected $table = 'users';
    public $timestams = false;
    // 下面即是允许入库的字段，数组形式，name age sex三个字段允许入库
    protected $fillable = ['name','avatar','open_id','token','user_sex','update_at','user_tel'];

    public function UsersModelInsert($userinfo){

        $result=  $this->insert($userinfo);
        return $result;
    }
//    后台登陆
    public function UsersModelSelect($userinfo){

        $users = $this->get()->where('name',$userinfo['name'])->where('password',$userinfo['password'])->first();
        if(!$users){
            return 'false';
        }
        return $users;
    }

//    微信登陆
    public function WxLogin($userinfo,$wxinfo){
//        判断是否曾经登陆
        $user = $this->get()->where('open_id',$wxinfo['openid'])->first();
        if($user){
//       openid不返回给前台
            unset($user['open_id'],$user['password']);
            return $user;
        }

        $user = [
            'name' => $userinfo['nickName'],
            'avatar'=> $userinfo['avatarUrl'],
            'open_id'=> $wxinfo['openid'],
            'token'=> 'one'.md5($wxinfo['openid'].time()),
            'user_sex'=> $userinfo['gender']
        ];
       $usernew =$this->create($user);
       if(!$usernew['user_id']){
           $usernew['user_id']=$usernew['id'];
       }
//       openid不返回给前台
        unset($usernew['open_id'],$usernew['password'],$usernew['id']);
       return $usernew;
    }

//    检查token
    public function checkToken($userid,$token){
        return $userid.$token;
        return $this->get()->where('user_id',$userid)->where('token',$token)->first();
    }

//    更新用户信息
    public function UpdateUser($userinfo,$userid){
        $res =  DB::table('users')->where('user_id', $userid)->update($userinfo);
        return $res;
    }

//    获取用户信息
    public function GetUserInfo($userid){
        return $this->get('user_id','user_tel',"name",'user_sex')->where('user_id',$userid)->first();
    }

//    获取code获取微信用户openid
    public function GetOpenid($code){
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".
            env('APPID')."&secret=".env('APPSECRET')."&js_code=".$code."&grant_type=authorization_code";

        //通过code换取网页授权access_token
        $res=file_get_contents($url);
        //对JSON格式的字符串进行编码
        return json_decode($res,true);
    }
//    通过token获取用户openid
    public function GetOpenidFromToken($token){
        return $this->where('token',$token)->get('open_id')->first();
    }

}
