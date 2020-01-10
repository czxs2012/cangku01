<?php
/**
 * Created by PhpStorm.
 * User.class: Administrator
 * Date: 2019-11-01
 * Time: 13:38
 */

namespace Home\Controller;


class LhdjController extends CommonController
{
    public function lhdj()
    {
        $this->display();
    }

    public function lhdjsm()
    {
        $ygmc = I('ygmc');
        $yggh = I('yggh');
        $tmbh = I('tmbh');
        $sql = "select top 1 a.* from scjhdj a,daksgx b where tmbh='$tmbh' and a.kh=b.kh and a.gxmc=b.gxmc and b.lhdj=1 and a.lhdj=0 order by a.px";
        $lhdj = M()->query($sql);
        $kh = $lhdj[0]['kh'];
        if (empty($kh)) {
            $json['status'] = 2;
            $this->ajaxReturn($json, 'json');
        } else {
            $sql = "select * from daksgx where kh='$kh' and lhdj=1";
            $res = M()->query($sql);
            if ($res==0) {
                //请先到款式档案设置领货登记工序
                $json['status'] = -1;
                $this->ajaxReturn($res, 'json');
            }
        }
        $zh = $lhdj[0]['zh'];
        $kslx = $lhdj[0]['kslx'];
        $ms = $lhdj[0]['ms'];
        $ysmc = $lhdj[0]['ysmc'];
        $sl = $lhdj[0]['sl'];
        $fzm = $lhdj[0]['fzm'];
        $gpm = $lhdj[0]['gpm'];
        $gxmc = $lhdj[0]['gxmc'];
        $tmbh = $lhdj[0]['tmbh'];
        $xh = $lhdj[0]['xh'];
        $px = $lhdj[0]['px'];
        $sb = $lhdj[0]['sb'];
        $sql = "insert into sclhdj(lhrq,ygmc,yggh,zh,kh,kslx,ms,ysmc,sl,fzm,gpm,gxmc,tmbh,xh,px,sb) 
        select getdate(),'$ygmc','$yggh','$zh','$kh','$kslx','$ms','$ysmc','$sl','$fzm','$gpm','$gxmc','$tmbh','$xh','$px','$sb'
          update scjhdj set lhdj=1 where gpm='$gpm'";
        $res = M()->execute($sql);
        if($res){
            $json['status']=1;
            $json['zh'] =$zh;
            $json['kslx'] = $kslx;
            $json['ms'] = $ms;
            $json['ysmc'] = $ysmc;
            $json['tmbh']=$tmbh;
            $json['xh']=$xh;
            $json['sl']=$sl;
            $json['gpm'] = $gpm;
            $json['gxmc'] = $gxmc;
            $json['px']=$px;
            $json['sb']=$sb;
            $this->ajaxReturn($json,'json');

        }

    }

}