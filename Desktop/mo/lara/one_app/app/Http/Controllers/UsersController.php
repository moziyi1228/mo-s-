<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Users;
class UsersController extends Controller
{
    public function show($id){


//        dd('测试');
        $users=DB::select('select * from adminuser where id = ?',[$id]);
        return $users;
    }


//    //调用模型里的insert
//    public function  UserInsert(){
//        $userinfo=$_POST;
////
//        $userinfo=[
//            'name'=>'machenxin',
//            'password'=>'4616',
//            'email'=>'fjdsla'
//        ];
//        $Users=new Users;
//        $result = $Users -> UsersModelInsert($userinfo);
//        if($result){
//            return view('index',compact('result'));
//        }else{
//            return '404';
//        }
//    }
//


    //调用模型里的select
    public function  UserSelect($userinfo){

        $userinfo=array(
            'name'=>'莫恣壹',
            'password'=>'1234'
        );
        $Users=new Users;
        $result = $Users -> UsersModelSelect($userinfo);
        if($result!='false'){
            return redirect('index');
        }
        return redirect('login');
    }


}
