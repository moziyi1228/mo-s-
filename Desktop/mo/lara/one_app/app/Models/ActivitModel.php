<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivitModel extends Model
{
    use HasFactory;
    //必须
    protected $table = 'activit';
    public $timestams = false;

    // 下面即是允许入库的字段，数组形式，name age sex三个字段允许入库
    protected $fillable = ['actid','userid','enrolltime','order_num'];

    public function UsersModelInsert($userinfo){

        $result=  $this->insert($userinfo);
        return $result;
    }

//    查询活动信息

    public function GetAct($actid = null,$page=1,$pagesize=8){
        if($actid === null ){
            $act['list'] = $this->orderbyDesc('actid')->skip(($page-1)*$pagesize)->take($pagesize)->get();
            $total = $this->count();
            $act['total']=$total;
            return $act;
        }
        if($actid !== null) {
            $actinfo = $this->get()->where('actid', $actid)->first();
            return $actinfo;
        }

    }

//   创建活动
    public function CreteAct($actinfo){
        $actinfo['starttime']=date('Y/m/d H:i',strtotime($actinfo['starttime'])) ;
        $actinfo['endtime']=date('Y/m/d H:i',strtotime($actinfo['endtime'])) ;
        $result = $this->insert($actinfo);
        return $result;
    }

//    查询是否已经报名
    public function IsSignUp($userid,$actid){
        $reslut = DB::table('enroll_list')->get()->where('statu',1)->where('effective',0)->where("userid",$userid)->where("actid",$actid)->first();
        return $reslut;
    }
    //    查询是否已经创建报名
    public function IsCreateSignUp($userid,$actid){
        $reslut = DB::table('enroll_list')->get()->where('effective',0)->where("userid",$userid)->where("actid",$actid)->first();
        return $reslut;
    }
//    报名活动创建报名记录
    public function SignUpAct($userid,$actid,$pay){
//        判断是否已经chuangjian报名
        if($this->IsCreateSignUp($userid,$actid)){
            return DB::table('enroll_list')->where('userid',$userid)->where('actid',$actid)->where('effective',0)->get('order_num')->first();
        }
//        创建插入报名数据数组
        $info=[
            'userid'=>$userid,
            'actid'=>$actid,
            'enrolltime'=>date('Y/m/d H:i:s'),
//        生成订单编号
            'order_num'=>'oneapp'.time().$userid.$actid.rand(10000,99999),
            'pay'=> $pay
        ];

        $result = DB::table('enroll_list')->insert($info);
        if($result){
            return DB::table('enroll_list')->where('userid',$userid)->where('actid',$actid)->where('effective',0)->get('order_num')->first();
        }
        return false;
    }


//    支付成功，更新报名表支付信息
    public function Pay($userid,$actid){
        return DB::table('enroll_list')->where('userid',$userid)->where('actid',$actid)->where('effective',0)->update(['statu'=>1]);
    }

}
