<?php

namespace App\Http\Controllers;
use App\Http\Controllers\ActivitController;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\Controller;
use App\Models\UsersModel;
use http\Env\Response;
use Illuminate\Http\Request;
//支付
use GuzzleHttp;
use WechatPay\GuzzleMiddleware\Util\PemUtil;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;
// 参考上上述说明，引入 `SensitiveInfoCrypto`
use WechatPay\GuzzleMiddleware\Util\SensitiveInfoCrypto;

class WeChatController extends Controller
{
    private static $mchid = '1630134826';

//    发起统一下单 请求api https://api.mch.weixin.qq.com/pay/unifiedorder
    public function WechatPay(Request $request){
        $token = $request->header('Authorization');
//        获取openid
        $user = new UsersModel();
        $res = $user->GetOpenidFromToken($token);
//        创建报名返回报名号码 order_num
        $signinfo = ActivitController::SignUp($token,$request['useid'],$request['actid'],$request['pay']);
        if($signinfo ==='登陆有误'){
            return response()->json($signinfo);
        }

        // 商户相关配置
        $merchantId 	      = self::$mchid; // 商户ID
        $merchantSerialNumber = '3F135DED3A89C7884B91B1A3A9BEADD6C0734FBF'; // 商户API证书序列号
        $merchantPrivateKey   = PemUtil::loadPrivateKey( public_path('1630134826_20220816_cert/apiclient_key.pem')); // 商户私钥的绝对路径
        // 微信支付平台配置【注意：这个参数需要另外生成】
        $wechatPayCertificate = PemUtil::loadCertificate( public_path('1630134826_20220816_cert/cert.pem')); 		  // 微信支付平台证书的绝对路径

        // 构造一个WechatPayMiddleware
        $wechatPayMiddleware = WechatPayMiddleware::builder()
            ->withMerchant($merchantId, $merchantSerialNumber, $merchantPrivateKey) // 传入商户相关配置
            ->withWechatPay([ $wechatPayCertificate ]) // 可传入多个微信支付平台证书，参数类型为array
            ->build();
        // 将WechatPayMiddleware添加到Guzzle的HandlerStack中
        $stack = GuzzleHttp\HandlerStack::create();
        $stack->push($wechatPayMiddleware, 'wechatpay');

        // 创建Guzzle HTTP Client时，将HandlerStack传入
        $client = new GuzzleHttp\Client(['handler' => $stack]);
        // 接下来，正常使用Guzzle发起API请求，WechatPayMiddleware会自动地处理签名和验签
        $query = [
            'appid' => 'wxec569e12169d47b8', 			// 小程序APPID
            'mchid' => $merchantId,	// 商户号
            'description'  => $request['actname'].'-报名', 	// 商品描述
            'out_trade_no' => $signinfo['order_num'], 	// 商户订单号
            'time_expire'  => date(DATE_RFC3339,strtotime("+10 minutes",time())), 	// 交易结束时间 DATE_RFC3339格式 10分钟
            'notify_url'   => 'https://www.one4616.club/api/wechat/notify_url',	// 通知地址
            'amount' => [
                'total' => $request['pay'],		// 金额
                'currency' => 'CNY'	// 货币类型
            ],
            'payer' => [
                'openid' => $res['open_id']		// 用户标识
            ]
        ];
        try {
            // 以下内容根据自身需求填写，这是小程序支付统一下单API
            $resp = $client->request('POST', 'https://api.mch.weixin.qq.com/v3/pay/transactions/jsapi',
                [
                    'body' =>json_encode($query),
                    'headers' => [ // 请求头
                        'Accept' 	   => 'application/json',
                        'Content-Type' => 'application/json',
                        'User-Agent'   => request()->userAgent()
                    ]
                ]);
//            echo $resp->getStatusCode().' '.$resp->getReasonPhrase()."\n";
//            echo $resp->getBody()."\n";
//            构造签名
            $result['code']=$resp->getStatusCode();
//            时间戳
            $time =  time();
//            随机数
            $nonceStr = $this->getRandomString(16);
//            统一下单id
            $prepay_id = json_decode($resp->getBody(),true)['prepay_id'];
//            构建签名
            $certKey = file_get_contents(public_path('1630134826_20220816_cert/apiclient_key.pem'));

            $string =$query['appid']."\n".$time."\n".$nonceStr."\nprepay_id=".$prepay_id."\n";

            openssl_sign($string, $raw_sign, $certKey, 'sha256WithRSAEncryption');
            $sign = base64_encode($raw_sign);

            $result=array(
                'message'=>[
                    'pay'=>[
                        'timeStamp'=> strval($time),
                        'nonceStr'=> $nonceStr,
                        'package' => "prepay_id=".$prepay_id,
                        'paySign' =>$sign,
                        'signType' => 'RSA',
//                        'appId' => $query['appid']
                    ],
                    'order_number'=> $signinfo['order_num']
                ],
                'meta'=>[
                    'msg'=>'预付订单生成成功',
                    'status'=> 200
                ]
            );

            return response()->json($result);
        } catch (RequestException $e) {
            $result=array(
                'meta'=>[
                    'msg'=>$e->getMessage(),
                    'status'=> 201
                ]
            );

            return response()->json($result);
            // 进行错误处理
            echo $e->getMessage()."\n";
            if ($e->hasResponse()) {
                echo $e->getResponse()->getStatusCode().' '.$e->getResponse()->getReasonPhrase()."\n";
                echo $e->getResponse()->getBody();
            }
            return;
        }

  }

//    生成随机数
    function getRandomString($len, $chars=null)
    {
        if (is_null($chars)) {
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        }
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++) {
            $str .= $chars[mt_rand(0, $lc)];
        }
        return $str;
    }


//    WechatNotify微信通知支付结果
    public function  WechatNotify(){
        // 接收微信推送的数据
        $data = file_get_contents('php://input');
        // 将接收的数据写入日志文件

        $path = app()->storagePath('wxPay/' . date('Ym'));

        if (!file_exists($path)) {

            mkdir($path, 0755, true);

        }
        $path .= '/' . date('Ymd') . '.txt';
        file_put_contents($path, date('Y-m-d H:i:s') . "\n Content:" . json_encode($data) . "\n\n", FILE_APPEND);
        echo "success";

    }

//    微信支付成功后报名成功
    public static function SuccessSign(Request $request){
        $signinfo = ActivitController::Pay($request['useid'],$request['actid']);
        if($signinfo){
            $json=[
                'meta'=>[
                    'msg'=>'报名支付成功',
                    'status'=> 200
                ]
            ];
        }else{
            $json=[
                'meta'=>[
                    'msg'=>'报名失败',
                    'status'=> 201
                ]
            ];
        }

        return response()->json($json);
    }

//    查询订单支付状态
    public function CheckOrder(Request $request){
        $order_num = $request['ordernum'];
        // 商户相关配置
        $merchantId 	      = self::$mchid; // 商户ID
        $merchantSerialNumber = '3F135DED3A89C7884B91B1A3A9BEADD6C0734FBF'; // 商户API证书序列号
        $merchantPrivateKey   = PemUtil::loadPrivateKey( public_path('1630134826_20220816_cert/apiclient_key.pem')); // 商户私钥的绝对路径
        // 微信支付平台配置【注意：这个参数需要另外生成】
        $wechatPayCertificate = PemUtil::loadCertificate( public_path('1630134826_20220816_cert/cert.pem')); 		  // 微信支付平台证书的绝对路径

        // 构造一个WechatPayMiddleware
        $wechatPayMiddleware = WechatPayMiddleware::builder()
            ->withMerchant($merchantId, $merchantSerialNumber, $merchantPrivateKey) // 传入商户相关配置
            ->withWechatPay([ $wechatPayCertificate ]) // 可传入多个微信支付平台证书，参数类型为array
            ->build();
        // 将WechatPayMiddleware添加到Guzzle的HandlerStack中
        $stack = GuzzleHttp\HandlerStack::create();
        $stack->push($wechatPayMiddleware, 'wechatpay');

        // 创建Guzzle HTTP Client时，将HandlerStack传入
        $client = new GuzzleHttp\Client(['handler' => $stack]);
        // 接下来，正常使用Guzzle发起API请求，WechatPayMiddleware会自动地处理签名和验签

        $resp = $client->request('GET','https://api.mch.weixin.qq.com/v3/pay/transactions/out-trade-no/'.$order_num.'?mchid='.$merchantId, [
            'headers' => [ // 请求头
                'Accept' 	   => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent'   => request()->userAgent()
            ]
        ]);
        $res=json_decode($resp->getBody(),true);
            if($res['trade_state']==='SUCCESS'){
                $signinfo = ActivitController::Pay($request['useid'],$request['actid']);
                if($signinfo){
                    $json=[
                        'meta'=>[
                            'msg'=>'报名支付成功',
                            'status'=> 200
                        ]
                    ];
                }else{
                    $json=[
                        'meta'=>[
                            'msg'=>'报名失败',
                            'status'=> 201
                        ]
                    ];
                }
            }else{
                $json=[
                    'meta'=>[
                        'msg'=>'支付失败',
                        'status'=> 202
                    ]
                ];
            }
       return response()->json($json);
    }

}