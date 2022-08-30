<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use  App\Http\Controllers\UserLoginController;
use App\Http\Controllers\IndexController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\ActivitController;
use App\Http\Controllers\FrontController;
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

//管理后台
//后台首页
Route::get('/{index?}/{userid?}', [IndexController::class,'index'])->where('index','[index]+');
//后台登陆页
Route::get('login/{id?}', function (Request $request) {
    return view('login');
});
//后台创建活动页
Route::get('/cact',[IndexController::class,'CreatAct']);
//后台活动列表页
Route::get('/actlist',[IndexController::class,'GetAct']);
//后台删除活动
Route::get('/delact/{actid}',[ActivitController::class,'DelAct']);
//前台活动页面
Route::get("/activit/{actid?}",[ActivitController::class,'Activit'])->withoutMiddleware('checklogin');



//后台登陆验证
Route::post("/userlogin",[UserLoginController::class,'UserLogin'])->withoutMiddleware('checklogin');

//后台退出登陆
Route::get("/userlogout/{userid?}",[UserLoginController::class,'UserLogout'])->withoutMiddleware('checklogin');


//后台登陆访问
Route::post('/login/select',[UsersController::class,'UserSelect']);

//后台创建管理员

//获取用户信息
Route::get('/users/{id}',[UsersController::class,"show"]);


//前台首页
Route::get('/front/index',[FrontController::class,'index'])->withoutMiddleware('checklogin');;
//前台用户信息页
Route::get('/front/profilesetting',[FrontController::class,'ProfileSetting'])->withoutMiddleware('checklogin');;
//照片文件处理
Route::post('/Photo',[\App\Http\Controllers\FileController::class,'TemporaryPhoto'])->withoutMiddleware('checklogin');;


//重定向测试
Route::get('abc', function () {
    route::redirect('abc','/');
});


//写入usermodel
Route::any('/UserInsert',[UsersController::class,'Userinsert']);






//test
Route::get('/test',function(){
//    session()->forget();
    $userinfo['name'] = '莫恣壹';
    $userinfo['password'] = '1234';
    $user=new UserLoginController();
    $a=$user->UserLogin($userinfo);
      session()->push('userinfo',$a[0]);
//    session()->save();
    dd (session('userinfo'));
})->withoutMiddleware('checklogin');
