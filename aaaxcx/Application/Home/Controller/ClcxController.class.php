<?php
/**
 * Created by PhpStorm.
 * User.class: Administrator
 * Date: 2019-10-31
 * Time: 10:23
 */

namespace Home\Controller;

class ClcxController extends CommonController
{
    public function index(){

    }
    public function clcx(){
        $ygmc = I('ygmc');
        $yggh = I('yggh');
        $ksrq = I('ksrq')." 00:00:00";
        $jsrq = I('jsrq')." 23:59:59";
        $sql="select kh,zh,tmbh,gxmc,ysmc,ms,ceiling(jhsl)as jhsl,CONVERT(varchar(100), jhrq, 23) as jhrq,convert(nvarchar(30),  
                convert(decimal(18,6), dj)) as dj,convert(nvarchar(30),convert(decimal(18,6), je)) as je  from cmp
                where yggh='$yggh' and ygmc='$ygmc' and jhrq  between '$ksrq' and '$jsrq' order by jhrq,kh";
        $res = M()->query($sql);
        $sum_sql = " select count(jhrq) as zjl, isnull(ceiling(sum(jhsl)), 0) as zjhsl, convert(nvarchar(30),isnull(convert(decimal(18,6), sum(je)), 0)) as zje 
                        from cmp where yggh='$yggh' and ygmc='$ygmc' and cast(jhrq as datetime)  between '$ksrq' and '$jsrq' ";
        $top_sum = M()->query($sum_sql);
        $json['content'] = $res;
        $json['top_sum'] = $top_sum;
        $this->ajaxReturn($json,'json');
    }

}