<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-11-08
 * Time: 17:32
 */

namespace Home\Controller;
header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
header('Access-Control-Allow-Methods:GET, POST, OPTIONS');

use Home\Entity\Xgxjh;
use Think\Controller;

class XgxjhController extends Controller
{
    public function smxgxjh(){
        $xgx = new Xgxjh();
        $yggh = I('yggh');
        $tmbh = I('tmbh');
        $res = $xgx->smxgxjh($yggh,$tmbh);
        return $res;
    }


    //选工序交货
    public function xgxjh($tmbh,$gpm,$ygmc,$yggh){
        $sql="select * from scjhdj where gpm='$gpm'";
        $res = M()->query($sql);
        if(!empty($res)){
            $tmbh = $res[0]['tmbh'];
            $qhqr = $res[0]['qhqr'];
            $sql="select * from bcps where gpm='$gpm'";
            $res = M()->query($sql);
            $jhsl = $res[0]['bcps'];
            if($qhqr==0){  //不按顺序交货
                $sql="update scjhdj set jhsl='',je=$jhsl*dj,$ygmc='$ygmc',yggh='$yggh',
jhrq=convert(varchar(22),GETDATE(),120),lhrq=convert(varchar(22),GETDATE(),120) where gpm='$gpm' ";
                $res = M()->execute($sql);
            }

        }

    }
    public function qdjh(){
        $gpm = I('gpm');
        $yggh = I('yggh');
        $ygmc = I('ygmc');
        $sql="select * from scjhdj where gpm='$gpm'";
        $res = M()->query($sql);
        if(!empty($res)){
            $tmbh = $res[0]['tmbh'];
            $qhqr = $res[0]['qhqr'];
            $sql="select * from bcps where gpm='$gpm'";
            $res = M()->query($sql);
            $jhsl = $res[0]['bcps'];
            if($qhqr==0){
                //不按顺序直接修改
                $sql="update scjhdj set jhsl='$jhsl',je=$jhsl*dj,ygmc='$ygmc',yggh='$yggh',jhrq=convert(varchar(22),getdate(),120),
                lhrq=convert(varchar(22),getdate(),120) where gpm='$gpm' ";
                $res =  M()->execute($sql);
                if($res){
                    echo '1';
                    exit;
                }
            }elseif ($qhqr==1){
                //按顺序交货
                $sql="if object_id('#temdb..#scjhdjpd')is not null drop table ##scjhdjpd 
                 select *,row_number()over(order by px)as row into ##scjhdjpd from scjhdj
                 where tmbh='$tmbh' and qhqr=1 order by row ";
                $res = M()->execute($sql);
                $sql="select * from ##scjhdjpd where gpm='$gpm'";
                $res = M()->query($sql);
                if($res[0]['row']==1){  //判断是不是第一道工序，如是直接交货
                    $sql="update scjhdj set jhsl='$jhsl',je=$jhsl*dj,ygmc='$ygmc',yggh='$yggh',
                   jhrq=convert(varchar(22),GETDATE(),120) where gpm='$gpm'";
                    $res = M()->execute($sql);
                    if($res){
                        echo 1;exit;
                    }

                }else{

                    //若不是第一道工序，查询上一道工序
                    $row = $res[0]['row']-1;
                    $sql="select isnull(case when yggh<>'' then yggh else '' end,'')as yggh,gxmc from ##scjhdjpd where row='$row'";
                    $res = M()->query($sql);
                    //判断上一道工序是否交货
                    if($res[0]['yggh']==''){
                        //上一道没有交货
                        echo '2';exit;

                    }else{
                        //上一道已交货
                        $sql="update scjhdj set jhsl='$jhsl',je=$jhsl*dj,ygmc='$ygmc',yggh='$yggh',jhrq=convert(varchar(22),GETDATE(),120) where gpm='$gpm'";
                        $res = M()->execute($sql);
                        if($res){
                            echo 1;exit;
                        }
                    }

                }
            }else{
                echo 3;exit;

            }
          /*  $sql="select * from scjhdj where tmbh='$tmbh' and yggh='$yggh' and qhqr=0";
            $res = M()->query($sql);
            if(!empty($res)){
                $xgx = new Xgxjh();
                $res = $xgx->smxgxjh($yggh,$tmbh);
                $this->ajaxReturn($res,'json');
            }*/
        }
    }



}