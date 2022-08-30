<?php

namespace App\Http\Controllers;
use URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;
use App\Models\ActivitModel;
use momo\gongju;
//前台活动控制器
class FrontController extends Controller
{
    public function  __construct(){

        $this->middleware(function ($request, $next) {
            $user = session('user');
            if($user != null){
                $this->user = $user[0];
            };
            $this->user = ['name'=>'游客'];
            return $next($request);
        });
    }
    public function index(){
        

//        获取游客登陆信息

        return  view('frontdesk/index',['username'=>$this->user['name']]);
    }
//用户信息页
    public function ProfileSetting(){
        //        获取游客登陆信息
        return  view('frontdesk/user-setting');

    }



//    redis处理报名人数信息
    public function PutInRedis($info,$key,$time){
        if(Redis::setnx($key,$info)!==0){
            return Redis::expire($key,$time );
        }
        return false;

    }

}


