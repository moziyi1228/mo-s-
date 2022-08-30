<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redis;
use Psy\CodeCleaner\ReturnTypePass;


class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

//        获取请求路由
        $route = request()->path();
        //  获取session数据和redis数据对比
        $suser =session('user');
        if($suser === null){
//            判断是否是login
            if(strpos($route,'login')!==false){
                return $next($request);
            }
            return redirect('login');
        }
        $suser=$suser[0];
        $ruser=Redis::get('user:'.$suser['userid']);
        if($ruser === null){
            $request->session()->forget('user');
//            判断是否是login
            if(strpos($route,'login')!==false){
                return $next($request);
            }
            return redirect('login');
        }
        $ruser = json_decode($ruser,true);
        if($suser['user_id']==$ruser['user_id']&&$suser['token']=$ruser['token']){

           //  更新redis的登陆有效时间
            Redis::expire('user:'.$ruser['user_id'],600);
            //判断是否login
            if(strpos($route,'login')!==false){
                return redirect('index');
            }
            return $next($request);
        }
        return redirect('login');

    }

}
