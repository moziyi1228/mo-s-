<?php

namespace App\Http\Controllers;
use App\Models\UsersModel;
use phpDocumentor\Reflection\DocBlock\Tags\Uses;
use URL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Response;
use App\Models\ActivitModel;
use momo\gongju;
//前台活动控制器
class ActivitController extends Controller
{
    public function Activit(Request $request){
        //活动id获取活动信息
        $actid = $request['actid'];
        $act = new ActivitModel();
        $actinfo = $act ->GetAct($actid);
//        判断是否存在活动
        if($actinfo=='false'){
            dd("false");
        }
//        判断活动页是否存在，存在直接输出
        if(file_exists("activit:".$actid)){
           return response()->file("activit:".$actid);
        }
        //转换数据类型
        $actinfo=json_decode($actinfo,true);;
        //创建活动页再输出
        $url = $actinfo['url'];

        $pachong =  new gongju\PachongServer();
        $content = $pachong->crawByUrl($url);
//       生成活动页面文件
        file_put_contents("activit:".$actid,$content['content_html']);
        return response()->file("activit:".$actid);
    }

//获取活动信息
    public function GetActInfo(Request $request){
        //获取活动id
        $actid = $request['actid'];
        $act = new ActivitModel();
        $actinfo = $act ->GetAct($actid);
//        格式化时间
        $actinfo['starttime']=date('Y/m/d H:i',strtotime($actinfo['starttime'])) ;
        $actinfo['endtime']=date('Y/m/d H:i',strtotime($actinfo['endtime'])) ;
        if($actinfo) {
            return response()->json($actinfo);
        }
        return false;

    }

// 创建活动
    public function CreateAct(Request $request){
        $actinfo=$request->post();

        $url = $actinfo['url'];

        //获取file类型name为img的文件
        if($request->hasFile("img")){
            $img = $request->file('img');
            $filetype =$img->getClientOriginalExtension();
            $type = ['jpg','png','jepg'];

            if ($filetype && !in_array($filetype, $type)) {
                return ['error' => 'You may only upload png, jpg or gif.'];
            }
        }
        $destinationPath = 'uploads/images/';
        $fileName = time().'.'.'jpg';
//        保存图片
        $img->move($destinationPath, $fileName);
//        记录图片地址
        $actinfo['img']=asset($destinationPath.$fileName);
        $act = new ActivitModel();
        $result = $act->CreteAct($actinfo);
         return $result;
    }
//    删除活动
    public function DelAct(Request $request){
        $result = ActivitModel::where('actid','=',$request['actid'])->delete();
        if($result = 1){
//            删除成功
            return redirect(URL::previous());
        }else{
//            删除失败
            return redirect(URL::previous());
        }
    }

//    报名活动
    public function EnrollAct(Request $request){
//        获取报名锁
        return "ok";
    }


//    redis处理报名人数信息
    public function PutInRedis($info,$key,$time){
        if(Redis::setnx($key,$info)!==0){
            return Redis::expire($key,$time );
        }
        return false;

    }


//获取活动列表
    public function GetActList(Request $request){
        $page = $request['pagenum'];
        $pagesize = $request['pagesize'];
        $act = new ActivitModel();
        $actinfo = $act ->GetAct(null,$page,$pagesize);
        if($actinfo) {
            return response()->json($actinfo);
        }
        return false;
    }


//    用户报名活动创建报名信息
    public static function SignUp($token,$userid,$actid,$pay){
//        $token = $request->header('Authorization');
        $user = new UsersModel();
        //        判断token是否正确
        if($user->checkToken($userid,$token)){
            $activit = new ActivitModel();

            $result = $activit->SignUpAct($userid,$actid,$pay);
            if($result){
                return $result;
            }else{
                $msg['errMsg']='已经报名';
            }
        }else{
            $msg['errMsg']='登陆有误';
        }
        return $msg;
    }


//    判断是否报名
    public function IsSignUp(Request $request){
        $activit = new ActivitModel();
        $result = $activit->IsSignUp($request['userid'],$request['actid']);
       if($result){
           $msg['errMsg']='已报名';
       }else{
           $msg['errMsg']='未报名';
       }
       return response()->json($msg);
    }

//    报名支付成功
    public static function Pay( $userid,$actid){
        $activit = new ActivitModel();
        $result = $activit->Pay($userid,$actid);
        return $result;
    }


}


