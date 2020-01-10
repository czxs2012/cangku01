<?php
/**
 * Created by PhpStorm.
 * User.class: Administrator
 * Date: 2019-11-02
 * Time: 10:33
 */

namespace Home\Controller;


class GpsmController extends CommonController
{
    public function ygclcx()
    {
        $ygmc = I('ygmc');
        $yggh = I('yggh');
        $sql = "select count(jhrq)as jhcs,isnull(ceiling(sum(jhsl)),0) as zjhsl,isnull(convert(nvarchar(30),convert(decimal(18,2),sum(je))),0) as zje from cmp
          where ygmc='$ygmc' and yggh='$yggh' and jhrq between convert(varchar(100),GETDATE(),23)+' 00:00:00' 
          and convert(varchar(100),getdate(),23)+' 23:59:59'";
        $res = M()->query($sql);
        $this->ajaxReturn($res, 'json');
    }

    public function gpsmcx()
    {
        $tmbh = I('tmbh');
        $ygmc = I('ygmc');
        $yggh = I('yggh');
        $res = $this->cxjhsx();
        if ($res == false) {
            $this->cxjhgx($ygmc, $yggh, $tmbh);
        } else {
            $this->gpsmjh($tmbh, $ygmc, $yggh);

        }


    }

    public function gpsmjh($tmbh, $ygmc, $yggh)
    {
        $sql = "select top 1 fzm from scjhdj where jhrq='' and yydh_wfxs='' and tmbh='$tmbh'";
        $res = M()->query($sql);
        if (!empty($res)) {
            $sql = "select top 1 skrlx from xtsz";
            $res = M()->query($sql);
            //交货认类型
            if ($res[0]['skrlx'] == 1) {
                $sql = "select top 1 gpm,a.kh,qhqr,a.bz,a.gxmc,kh,dj,sl,ms,ysmc,zh from scjhdj a,(select gxmc,
          a.ygmc,a.yggh from daygda a,daygda_gxlx b,dagxda c where a.yggh=b.yggh and b.lx=c.lx and a.yggh='$yggh') as bb
          where a.gxmc=bb.gxmc and jhrq='' and bz='' and yydh_wfxs='' and tmbh='$tmbh' order px";

            } else {
                $sql = "select top 1 gpm,a.kh,qhqr,a.bz,a.gxmc,kh,dj,sl,ms,ysmc,zh from scjhdj a,(select b.gxmc,c.ygmc,c.yggh from dagxda b,dagx_ygfp c 
                  where b.xh=c.xh and c.yggh='$yggh') as bb where a.gxmc=bb.gxmc and jhrq='' and bz='' and yydh_wfxs=''
                    and tmbh='$tmbh' order by px
       ";
            }
            $res2 = M()->query($sql);
            $gpm = $res2[0]['gpm'];
            //工票码无效
            if ($gpm == '') {
                $sql = "select top 1 gxmc from scjhdj where jhrq='' and bz=''  and yydh_wfxs='' 
           and tmbh='$tmbh' order  by px";
                $res = M()->query($sql);
                //工票码无效，此卡的工序存在
                if ($res[0]['gxmc'] != '') {
                    //员工没有分配这道工序
                    $json['status'] = 0;
                    $this->ajaxReturn($json, 'json');
                }
            } else { //工票码有效

                $sql = "select top 1 gpm,gxmc from scjhdj where jhrq='' and bz='' and qhqr=1 and yydh_wfxs='' and 
                 tmbh='$tmbh' and px<(select px from scjhdj where jhrq='' and gpm='$gpm') order by px desc";
                $res = M()->query($sql);
                if (!empty($res)) {
                    //存在上一道工序，必须先扫上一道工序
                    $json['status'] = -1;
                    $json['gxmc'] = $res[0]['gxmc'];
                    $this->ajaxReturn($json, 'json');
                }

            }


        } else {
            $json['status'] = 2;
            $this->ajaxReturn($json, 'json');
        }

        //-------查询出在此工序最近的工序的数量是多少要用 jhsl交货的数量 order by px desc
        $sql = "select isnull(gxsl,0) as gxsl from scjhdj where gpm='$gpm' and tmbh='$tmbh'";
        $res = M()->query($sql);
        if ($res[0]['gxsl'] != 0) {
            $sql = "select top 1 isnull(gxsl,0)as sl from scjhdj where jhrq='' and bz='' and yydh_wfxs='' and
           ch in(select top 1 ch from scjhdj where gpm='$gpm') and tmbh='$tmbh' and px=(select px from scjhdj where jhrq='' and gpm='$gpm') order by px desc";
        } else {
            $sql = "select top 1 isnull(jhsl,0) as sl from scjhdj where jhrq<>'' and bz='' and yydh_wfxs='' and ch 
            in (select top 1 ch from scjhdj where jhrq='' and gpm='$gpm') and tmbh='$tmbh' and 
            px<(select px from scjhdj where jhrq='' and gpm='$gpm' ) order by px desc ";
        }
        $res = M()->query($sql);
        if (!empty($res)) {
            $sl = $res[0]['sl'];
        } else {
            $sql = "select isnull(sl,0) as sl from scjhdj where gpm='$gpm'";
            $res = M()->query($sql);
            $sl = $res[0]['sl'];
        }
        //进行交货
        $sql = "update scjhdj set jhsl=$sl,je=$sl*dj,ygmc='$ygmc',yggh='$yggh',jhrq=convert(varchar(22),GETDATE(),120) where gpm='$gpm'";
        $res = M()->execute($sql);
        if ($res) {
            $json['status'] = 1;
            $json['res'] = $res2;
            $json['gpm'] = $gpm;
            $json['sl'] = (float)$sl;
            $json['je'] = (float)$res2[0]['dj'] * (float)$sl;
            $json['sys'] = (float)$res2[0]['sl'] - (float)$sl;
            $this->ajaxReturn($json, 'json');

        }
    }

    //查询公司档案工资设置，无线扫码按工序交货是否打钩
    public function cxjhsx()
    {
        $sql = "select sfgxmc from xtsz";
        $res = M()->query($sql);
        if ($res[0]['sfgxmc'] == 1) {  //如果勾选的话则为1
            return false;
        }
    }

    //查询货物卡有没有不按顺序交货的工序
    public function cxjhgx($ygmc, $yggh, $tmbh)
    {

        /*        $sql = "select count(gxmc)as sl from dagx_ygfp a,dagxda b where a.yggh='$yggh' and a.xh=b.xh and b.gxmc in(
                         select a.gxmc from scjhdj a where tmbh='$tmbh' and a.yggh='$yggh' and qhqr=0) ";*/

        $sql="         select COUNT(*)as sl from (select gxmc from dagx_ygfp a,dagxda b where  a.yggh='$yggh' and a.xh=b.xh)a where a.gxmc in
         (select gxmc from scjhdj where tmbh='$tmbh'  and qhqr=0 and bz='' and yydh_wfxs='')";
        $res = M()->query($sql);
        $sl = $res[0]['sl'];
        if ($sl > 1) { //若不按顺序的工序的数量大于1则需要选工序交货
            $xgxjh = A('Xgxjh');
            $res = $xgxjh->smxgxjh();
            if (!empty($res)) {
                $this->ajaxReturn($res, 'json');
            }
        } else {
            $this->gpsmjh($tmbh, $ygmc, $yggh);
        }
    }


}