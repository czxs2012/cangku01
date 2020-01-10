<?php
/**
 * Created by PhpStorm.
 * User.class: Administrator
 * Date: 2019-11-04
 * Time: 13:42
 */

namespace Home\Controller;


class YggxController extends CommonController
{
    public function xgmm(){
        $yggh = I('yggh');
        $mm =  I('mm');
        $sql="update daygda set  mm='$mm' where yggh='$yggh'";
        $res = M()->execute($sql);
        if($res){
            $json['status']=1;
            $this->ajaxReturn($json,'json');
        }
    }

    public function cxbm(){
        $sql="select bm  from dabmda order by id";
        $res = M()->query($sql);
        $this->ajaxReturn($res,'json');
    }
    public function cxyg(){
        $bm =I('bm');
        $page = I('page',1);
        $sql="select * from (select ygmc,yggh,row_number() over(order by id)as rowid from daygda where bm='$bm')as a where a.rowid between $page and $page+10";
        $res = M()->query($sql);
        $this->ajaxReturn($res,'json');
    }

    //查询工序
    public function addCx(){
        $yggh = I('yggh');
        $sql ="select * from dagxda where xh not in(select xh from dagx_ygfp where yggh='$yggh') order by xh asc";
        $res = M()->query($sql);
        $this->ajaxReturn($res,'json');
    }

    //员工添加工序
    public function addTj(){
        $xh = I('arr');
        $xh = explode(',',$xh);
        $yggh = I('yggh');
        $sql ="select yggh,ygmc,jm,bm from daygda where yggh='$yggh'";
        $res = M()->query($sql);
        $yggh = $res[0]['yggh'];
        $ygmc = $res[0]['ygmc'];
        $jm = $res[0]['jm'];
        $bm = $res[0]['bm'];
        foreach ($xh as $val){
            $sql="insert into dagx_ygfp(xh,yggh,ygmc,jm,bm) select '$val','$yggh','$ygmc','$jm','$bm'";
            $res = M()->execute($sql);
        }

        if($res){
            $json['status']=1;
            $this->ajaxReturn($json,'json');
        }
    }

    //删除员工序
    public function del(){
        $xh = I('arr');
        $yggh = I('yggh');
        $xh = "'".str_replace(",","','",$xh)."'";
        $sql =" delete dagx_ygfp where xh in (".$xh. ") and yggh='$yggh'";
        $res = M()->execute($sql);
        if($res){
            $json['status']=1;
            $this->ajaxReturn($json,'json');
        }
    }
    public function cxgx(){
        $yggh = I('yggh');
        $sql="select b.gxmc,a.yggh,a.xh from dagx_ygfp a,dagxda b where a.xh=b.xh and a.yggh='$yggh' order by a.xh asc";
        $res = M()->query($sql);
        $this->ajaxReturn($res,'json');
    }

}