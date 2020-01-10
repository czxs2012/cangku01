<?php
/**
 * Created by PhpStorm.
 * User.class: Administrator
 * Date: 2019-11-02
 * Time: 9:31
 */

namespace Home\Controller;

class CjController extends CommonController
{
    public function cjjd(){
        $this->display();
    }

    public function cjjdcx(){
        $tmbh = I('tmbh');
        $sql="select ch,kh,gxmc,px,ceiling(ISNULL(SUM(sl), 0)) as sl,ceiling(isnull(sum(jhsl),0))as jhsl,
CEILING(ISNULL((sum(sl)-SUM(isnull(jhsl,0))),0))as wjsl,(case when bz<>'' then '作废' else '未作废' end)as bz from scjhdj where ch=(select top 1 ch from scjhdj where tmbh='$tmbh') group by ch,kh,gxmc,px,bz order by bz";
        $res = M()->query($sql);
        $this->ajaxReturn($res,'json');


    }

    public function cxjhsx(){
        $sql="select sfgxmc from xtsz";
        $res = M()->query($sql);
        if($res[0]['sfgxmc']==1){
            $json['status']=1;
            $this->ajaxReturn($json,'json');
        }
    }
    public function cxjhgx(){
        $yggh = I('yggh');
        $tmbh = I('tmbh');
        $sql="select gxmc from dagx_ygfp a,dagxda d where a.yggh='$yggh' and a.xh=b.xh and b.gxmc in(select a.gxmc 
from scjhdj a  where tmbh='$tmbh')";
        $res = M()->query($sql);
        if(!empty($res)){
            $json['status']=1;
            $this->ajaxReturn($json,'json');
        }

    }
    //输入款号查询对应的床号
    public function cxch(){
        $ksrq = I('ksrq');
        $jsrq = I('jsrq');
        $kh  = I('kh');
        $sql=" select distinct ch,kh from scjhdj where CONVERT(varchar(11),jhrq,120) between '$ksrq' and '$jsrq' and kh like '%$kh%'";
        $res = M()->query($sql);
        $this->ajaxReturn($res,'json');
    }
    //查询对应床号生产进度
    public function cxchjd(){
        $ch = I('ch');
        $sql="select kh,ygmc,kslx,sl,gxmc,jhsl,(sl-jhsl)as wjsl from scjhdj where ch='$ch'";
        $res=  M()->query($sql);
        $this->ajaxReturn($res,'json');

    }


}