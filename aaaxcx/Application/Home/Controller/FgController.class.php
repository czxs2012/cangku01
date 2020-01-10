<?php
/**
 * Created by PhpStorm.
 * User.class: Administrator
 * Date: 2019-11-01
 * Time: 17:01
 */

namespace Home\Controller;


class FgController extends CommonController
{
    public function fgjl(){
        $this->display();
    }

    //查询已交货记录
    public function fgjlcx(){
        $ksrq = I('ksrq')." 00:00:00";
        $jsrq = I('jsrq')." 23:59:59";
        $sql ="select CONVERT(varchar(100),rq,23)as rq,ygmc,kh,kslx,gxmc,ysmc,ms,sl,jhsl  from scfgdj where rq
 between '$ksrq' and '$jsrq' order by ygmc,kh,rq";
        $res = M()->query($sql);
        $this->ajaxReturn($res,'json');


    }

    public function fgdj(){
        $tmbh = I('tmbh');
        $sql="select gpm,zh,kh,ysmc,ms,gxmc,sl,dj,jhsl,(jhsl*dj)as je from scjhdj where tmbh='$tmbh' and ygmc<>''";
        $res = M()->query($sql);
        if(count($res)>0){
            $this->ajaxReturn($res,'json');
        }

    }

    //返工提交
    public function fgdjtj(){
        $gpm = I('gpm');
        $sql ="select top 1 * from scjhdj where gpm='$gpm' and ygmc<>''";
        $res = M()->query($sql);
      //  $tmbh = $res[0]['tmbh'];
        $yggh = $res[0]['yggh'];
        $ygmc = $res[0]['ygmc'];
        $kh = $res[0]['kh'];
        $kslx = $res[0]['kslx'];
        $gxmc = $res[0]['gxmc'];
        $ms = $res[0]['ms'];
        $ysmc = $res[0]['ysmc'];
        $sl = $res[0]['sl'];
        $jhsl = $res[0]['jhsl'];
        $zt = $res[0]['zt'];
        $bz = $res[0]['bz'];
        if(count($res)>0){
            $sql="insert into scfgdj(rq,yggh,ygmc,kh,kslx,gxmc,ms,ysmc,sl,jhsl,zt,bz) select getdate(),'$yggh','$ygmc','$kh',
              '$kslx','$gxmc','$ms','$ysmc','$sl','$jhsl','$zt','$bz'
              update  scjhdj set jhrq='',ygmc='',yggh='',lhrq='',je=0,jhsl=null where gpm='$gpm'";
            $res = M()->execute($sql);
            if($res){
                $json['status']=1;
                $this->ajaxReturn($json,'json');
            }
        }
    }

}