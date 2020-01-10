<?php
/**
 * Created by PhpStorm.
 * User.class: Administrator
 * Date: 2019-11-01
 * Time: 17:35
 */

namespace Home\Controller;


use Home\Entity\Qxkz;

class GpszController extends CommonController
{
    public function gpsz()
    {
        $this->display();
    }

    public function gpszcx()
    {
        $tmbh = I('tmbh');
        $sql = "select ch,sb,kh,ysmc,ms,sl,zh,ygmc,gxmc,yggh,zt,convert(varchar,convert(decimal(18,0),isnull(jhsl,0)))as jhsl, convert(varchar,convert(decimal(18,4),dj))as dj,je,
gpm,(case when bz<>'' then '作废' else '未作废' end)as bz from scjhdj where tmbh='$tmbh' order by px";
        $res = M()->query($sql);
        if (empty($res)) {
            $json['status'] = 0;
            $this->ajaxReturn($json, 'json');
        }
        $this->ajaxReturn($res, 'json');


    }

    public function xgccs()
    {
        $tmbh = I('tmbh');
        $ygmc = I('ygmc');
        $gpm = I('gpm');
        $sl = I('xgsl');
        $cz = I('cz');
        $rq = date('Y-m-d H:i:s');
        $sql = "select * from scjhdj where tmbh in (select tmbh from scjhdj where gpm='$gpm')
           and ch in (select ch from scjhdj where gpm='$gpm') and zt=0";
        $res = M()->query($sql);
        $ycccs = $res[0]['sl'];
        if ($cz == 1) {
            $sql = "update scjhdj set sl=$sl,yc=1,ycrq='$rq',jsr='$ygmc',ycccsl=$ycccs where zt=0 and
             tmbh in(select tmbh from scjhdj where gpm='$gpm')";
        } elseif ($cz == 2) {
            $sql = "update scjhdj set jhsl=$sl,je=$sl*dj,yc=1,ycrq='$rq',ycccsl=$ycccs where zt=0 and jhrq<>'' and tmbh=(select tmbh from scjhdj where gpm='$gpm')";
        } elseif ($cz == 3) {
            $sql = "update scjhdj set jhsl=$sl,sl=$sl,je=$sl*dj,yc=1,ycrq='$rq',ycccsl=$ycccs where zt=0 and jhrq<>'' and tmbh=(select tmbh from scjhdj where gpm='$gpm')";
        }
        $res = M()->execute($sql);
        if ($res) {
            $json['status'] = 1;
            $this->ajaxReturn($json, 'json');
        }
    }

    //修改半成品
    public function xgbcps()
    {
        $gpm = I('gpm');
        $sl = I('sl');
        $sql = "select jhrq from scjhdj where gpm='$gpm'";
        $res = M()->query($sql);
        //判断是否交货
        $sql = "update scjhdj set gxsl='$sl' where gpm='$gpm'";
        $res = M()->execute($sql);
        if ($res) {
            $json['status'] = 1;
            $this->ajaxReturn($json, 'json');
        }
    }

    //清除工序交货信息
    public function qcjhjl()
    {
        $gpm = I('gpm');
        $rq = getrq();
        $ygmc = I('ygmc');
        $tmbh = I('tmbh');
        $sql = "select ygmc,zt,bz from scjhdj where gpm='$gpm'";
        $res = M()->query($sql);
        //查看是否作废
        if (($res[0]['zt'] != 1) && (empty($res[0]['bz']))) {
            $sql = "update scjhdj set jhsl=0,dytm=0,jhrq='',ygmc='',yggh='',yc=1,ycrq='$rq',ycygmc='$ygmc',jsr='$ygmc',
            je=0 where zt=0 and gpm='$gpm'";
            $res = M()->execute($sql);
            if ($res) {
                $json['status'] = 1;
                $this->ajaxReturn($json, 'json');

            }
        } elseif ($res[0]['zt'] == 1) { //已结单记录不能删除
            $json['status'] = -1;
            $this->ajaxReturn($json, 'json');
        } elseif ($res[0]['bz'] == 1) { //已作废记录不能删除
            $json['status'] = -2;
            $this->ajaxReturn($res, 'json');

        }
    }

    //作废工序

    public function zfgx()
    {
        $gpm = I('gpm');
        $cz = I('cz');
        $rq = getrq();
        $ygmc = I('ygmc');
        $tmbh = I('tmbh');
        switch ($cz) {
            case 1://此床此工序作废
                $sql = "select top 1 * from scjhdj where ch=(select ch from scjhdj where gpm='$gpm' ) and zt=0";
                $res = M()->query($sql);
                if (!empty($res)) { //此床是如果未结单则可以作废
                    $sql = "update scjhdj set bz='此工序作废',ycrq='$rq',jsr='$ygmc' where ch=(select ch from scjhdj where gpm='$gpm')
                     and gxmc=(select gxmc from scjhdj where gpm='$gpm') and zt=0";

                    $res = M()->execute($sql);
                    if ($res) {
                        $json['status'] = 1;
                        $this->ajaxReturn($json, 'json');
                    }
                } else { //此床已结单无法作废
                    $json['status'] = -1;
                    $this->ajaxReturn($json, 'json');
                }
                break;
            case 2: //取消此床此工序作废
                //判断是否有拆解
                $sql = "select cj from scjhdj  where ch =(select ch from scjhdj where gpm='$gpm') and cj=1";
                $res = M()->query($sql);
                if (empty($res)) {
                    $sql = "update scjhdj set bz='',ycrq='$rq',jsr='$ygmc' where ch=(select ch from scjhdj where gpm='$gpm') and gxmc=(select gxmc from scjhdj where gpm='$gpm') and zt=0";
                    $res = M()->execute($sql);
                    if (!empty($res)) {
                        $json['status'] = 1;
                        $this->ajaxReturn($json, 'json');
                    } else {
                        $json['status'] = 0;
                        $json->ajaxReturn($json, 'json');
                    }
                }else{
                    //工序删除拆解
                    $sql="delete scsddj where cjgpm in(select gpm from scjhdj where ch in(select ch from scjhdj where gpm='$gpm') and cj=1 ) ";
                    $res = M()->execute($sql);
                    $sql="update scjhdj set bz='',ycrq='$rq',jsr='$ygmc',cj=0 where ch in(select ch from scjhdj where gpm='$gpm') and gxmc=(select gxmc from scjhdj 
                    where gpm='$gpm') and zt=0 ";
                    $res = M()->execute($sql);
                    if($res){
                        $json['status']=1;
                        $this->ajaxReturn($json,'json');

                    }


                }
                break;
            case 3:
                //此扎作废
                $sql = " select top 1 * from scjhdj where fzm in(select fzm from scjhdj where gpm='$gpm') and zt=0";
                $res = M()->query($sql);
                //此扎是否已经全部结算
                if (!empty($res)) {
                    $sql = "update scjhdj set bz='此扎作废',ycrq='$rq',jsr='$ygmc' where zt=0  and fzm in (select fzm from scjhdj where gpm = '$gpm')";
                    $res = M()->execute($sql);
                    if (!empty($res)) {
                        //此扎作废成功
                        $json['status'] = 1;
                        $this->ajaxReturn($json, 'json');
                    }

                } else {
                    //此扎所有工序已经结算不能作废
                    $json['status'] = 0;
                    $this->ajaxReturn($json, 'json');
                }
                break;
            case 4: //取消此扎作废
                //判断是否有拆解的工序
                $sql = "select cj from scjhdj where fzm in(select fzm from scjhdj where gpm='$gpm') and cj=1";
                $res = M()->query($sql);
                if (empty($res)) {
                    $sql = "update scjhdj set bz='',ycrq='$rq',jsr='$ygmc' where  zt=0 and fzm 
                   in(select fzm from scjhdj where gpm='$gpm')";
                    $res = M()->execute($sql);
                    if ($res) {
                        $json['status'] = 1;
                        $this->ajaxReturn($json, 'json');
                    }

                } else { //工序有拆解
                    //删除工序拆解
                    $sql = "delete scsddj where cjgpm in(select gpm from scjhdj where fzm in(select fzm from scjhdj where gpm='$gpm') and cj=1) ";
                    $res = M()->execute($sql);
                    if ($res) {
                        //取消作废成功
                        $json['status'] = 1;
                        $this->ajaxReturn($json, 'json');
                    }
                }
                break;
            case 5://此卡此工序作废
                $sql = " select top 1* from scjhdj where gpm='$gpm' and zt=0";
                $res = M()->query($sql);
                if (!empty($res)) {
                    $sql = "update scjhdj set bz='此工序作废',ycrq='$rq',jsr='$ygmc' where gpm='$gpm' 
                   and zt=0";
                    $res = M()->execute($sql);
                    if ($res) {
                        //此卡此工序作废
                        $json['status'] = 1;
                        $this->ajaxReturn($json, 'json');
                    }
                }
            case 6://取消此卡此工序作废
                //查看是否有拆解工序
                $sql = "select cj from scjhdj where cj=1 and gpm='$gpm'";
                $res = M()->query($sql);
                if (empty($res)) { //没有拆解工序
                    $sql = "update scjhdj set bz='',ycrq='$rq',jsr='$ygmc' where gpm='$gpm' and zt=0";
                    $res = M()->execute($sql);
                    if ($res) {
                        $json['status'] = 1;
                        $this->ajaxReturn($json, 'json');
                    }
                } else {
                    //删除拆解
                    $sql = "delete scsddj where cjgpm='$gpm'";
                    M()->execute($sql);
                    //取消作废
                    $sql = "update scjhdj set bz='',cj='',ycrq='',jsr='$ygmc' where gpm='$gpm' and zt=0";
                    $res = M()->execute($sql);
                    if ($res) {
                        $json['status'] = 1;
                        $this->ajaxReturn($json, 'json');
                    }
                }
                break;
            default :
                $json['status'] = 0;
                $this->ajaxReturn($json, 'json');


        }
    }

    //修改交货信息
    public function xgjhxx()
    {
        $gpm = I('gpm');
        $yggh = I('yggh');
        $ygmc = I('ygmc');
        $cz = I('cz');
        $jhsl = I('jhsl');
        $jhrq = I('jhrq');
        $rq = getrq();
        //$jhrqpd = $_SESSION['jhrqpd'];
        $jsr = I('jsr');
        //   $tmbh = I('tmbh');
        /* $sql = "select ygmc from daygda where yggh='$yggh'";
         $rs = M()->query($sql);
         $ygmc = $rs[0]['ygmc'];*/
        //仅修改此工序
        if ($cz == 1) {
            $sql = "update scjhdj set ygmc='$ygmc',yggh='$yggh',jhsl='$jhsl',jhrq=convert(varchar(20),getdate(),20),je=dj*$jhsl,jsr='$jsr',
          yc=1,ycrq='$rq' where gpm='$gpm' and zt=0";
            $res = M()->execute($sql);
            if ($res) {
                $json['status'] = 1;
                $this->ajaxReturn($json, 'json');
            }
        }
        if ($cz == 2) {
            $sql = "update scjhdj set jhsl=$jhsl,je=dj*$jhsl,jsr='$jsr' where tmbh=(select tmbh from scjhdj
                where gpm='$gpm') and px >=(select  px from scjhdj where gpm='$gpm') and tmbh=(select tmbh from scjhdj 
                where gpm='$gpm') and ygmc<>''";
            $res = M()->execute($sql);
            if ($res) {
                $json['status'] = 1;
                $this->ajaxReturn($json, 'json');
            }
        }
    }

    //判断工序是否已经交货
    public function pdshjh()
    {
        $gpm = I('gpm');
        $sql = "select (case when ygmc='' then '未交货' else ygmc end )as ygmc,yggh,isnull(ceiling(jhsl),0)as jhsl,
        jhrq from scjhdj where gpm='$gpm'";
        $res = M()->query($sql);
        $ygmc = $res[0]['ygmc'];
        // $jhrq = explode(" ",$res[0]['jhrq']);
        //session_start();
        //$_SESSION['jhrq'] = $jhrq[0];
        if ($ygmc != '未交货') {

            $this->ajaxReturn($res, 'json');
        }
    }

    //下拉获取其他员工
    public function cxqtyg()
    {
        $yggh = I('yggh');
        $sql = "select ygmc,yggh  from daygda where yggh<>'$yggh'";
        $res = M()->query($sql);
        $this->ajaxReturn($res, 'json');
    }


}