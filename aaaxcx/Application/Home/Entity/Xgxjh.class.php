<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-11-11
 * Time: 10:19
 */

namespace Home\Entity;


class Xgxjh
{
    public function smxgxjh($yggh,$tmbh){
        $sql="select dj,ch,sb,zh,kh,ysmc,ms,gxmc,jhrq,px,sl,isnull(ceiling(jhsl),0)as jhsl,je,gpm,qhqr from scjhdj where tmbh='$tmbh'
         and gxmc in(select b.gxmc from dagx_ygfp a,dagxda b where a.yggh='$yggh' and a.xh=b.xh) order by qhqr,px";
        $res = M()->query($sql);
        return $res;
    }

}