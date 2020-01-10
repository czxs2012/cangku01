<?php
/**
 * Created by PhpStorm.
 * User.class: Administrator
 * Date: 2019-11-01
 * Time: 14:51
 */

namespace Home\Controller;

class LhcxController extends CommonController
{
    public function lhcx(){
        $this->display();
    }
    public function lhcxcx(){
        $ksrq = I('ksrq')." 00:00:00";
        $jsrq = I('jsrq')." 23:59:59";
        $sql ="select convert(varchar,lhrq,23) as lhrq,ygmc,kh,ysmc,ms,zh,gxmc,tmbh,sb,sl from sclhdj
  where cast(lhrq as datetime) between  '$ksrq' and '$jsrq' order  by ygmc,kh,lhrq";
        $res = M()->query($sql);
        $this->ajaxReturn($res,'json');


    }

}