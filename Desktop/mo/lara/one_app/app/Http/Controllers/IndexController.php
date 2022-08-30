<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Models\ActivitModel;

//后台管理控制器
class IndexController extends Controller
{

  public function Index(Request $request){
//    获取登陆用户信息
     $user =  session('user')[0];
      return view('index',['token'=>$user['token'],'userid'=>$user['userid'],'username'=>$user['name']]);
  }

//  活动列表
    public function GetAct(){
        $user = session('user')[0];
        //实例化活动模型
        $act= new ActivitModel();
        $actinfo = $act -> GetAct();
//        转换成数组
        $actinfo=json_decode($actinfo);
        return view('getact',['token'=>$user['token'],'userid'=>$user['userid'],'username'=>$user['name'],'actinfo'=>$actinfo]);
    }

   public function CreatAct(){
       $user =  session('user')[0];
       return view('cact',['token'=>$user['token'],'userid'=>$user['userid'],'username'=>$user['name']]);
   }
}
