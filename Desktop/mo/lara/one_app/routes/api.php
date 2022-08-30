<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ActivitController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//活动链接
Route::get('getactinfo/{actid}', [ActivitController::class,'GetActInfo']);

//创建活动
Route::post('createact', [ActivitController::class,'CreateAct']);

//报名活动
Route::get('enrollact/{actid}', [ActivitController::class,'EnrollAct']);
//获取活动列表
Route::get('getactlist', [ActivitController::class,'GetActList']);

//获取活动详情

Route::get('getactinfo', [ActivitController::class,'GetActInfo']);


//微信用户登陆

Route::post('user/wxlogin', [\App\Http\Controllers\UserLoginController::class,'WxLogin']);

//用户报名
Route::post('activit/signup', [\App\Http\Controllers\ActivitController::class,'SignUp']);

//判断是否已经报名
Route::post('activit/issignup', [\App\Http\Controllers\ActivitController::class,'IsSignUp']);

//获取微信授权手机号码
Route::post('user/getphonenum', [\App\Http\Controllers\UserLoginController::class,'GetPhoneNum']);

//发起报名
Route::post('pay/enrollment', [\App\Http\Controllers\ActivitController::class,'Enrollment']);

//微信支付
Route::post('wechat/wechatpay',[\App\Http\Controllers\WeChatController::class,'WechatPay'] );

//接收微信支付结果

Route::post('wechat/notify_url',[\App\Http\Controllers\WeChatController::class,'WechatNotify'] );

//接收微信支付结果

Route::post('wechat/paysuccess',[\App\Http\Controllers\WeChatController::class,'SuccessSign'] );

//查询微信支付结果接口

Route::post('wechat/checkOrd',[\App\Http\Controllers\WeChatController::class,'CheckOrder'] );
