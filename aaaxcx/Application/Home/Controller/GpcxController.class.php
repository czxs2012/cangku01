<?php
/**
 *工票查询
 *
 */
namespace Home\Controller;


class GpcxController extends CommonController
{
    public function gpcx(){
        $this->display();
    }

    public function cxkhtp(){
        $kh = I('kh');
        $sql ="select tp from daks_tp where kh='$kh'";
        $res = M()->query($sql);
        if($res[0]['tp']!=''){
            header('Content-type: image/jpg');
            echo $res[0]['tp'];
        }
    }
    public function cxkhtp1(){
        $kh = I('kh');
        $sql ="select tp2 from daks_tp where kh='$kh'";
        $res = M()->query($sql);
        if($res[0]['tp2']!=''){
            header('Content-type: image/jpg');
            echo $res[0]['tp2'];
        }
    }

    public function  gpcxcx(){
//        $yggh = I('yggh');
        $gpm = I('tmbh');
        $sql ="select ch,sb,a.kh,ysmc,ms,CEILING(sl)as sl,zh,ygmc,gxmc,CONVERT(nvarchar(30),ISNULL(CEILING(jhsl),0)) as jhsl,CONVERT(nvarchar(30),CONVERT(decimal(18,6),dj))as dj,
CONVERT(nvarchar(30),CONVERT(decimal(18,6),je))as je,(case when bz<> '' then '作废' else '未作废' end)as bz,zt from scjhdj a where tmbh='$gpm'  order by px
       ";
       $res = M()->query($sql);
        if(empty($res)){
            $json['status']=0;
            $this->ajaxReturn($json,'json');
        }
        $this->ajaxReturn($res,'json');
    }



}