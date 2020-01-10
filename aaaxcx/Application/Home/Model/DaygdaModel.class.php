<?php
namespace  Home\Model;

use Think\Model;

class DaygdaModel extends Model
{
    public function yzdl($qxkh){
        $sql="select * from daygda where  qxkh='$qxkh'";
        $res =$this->db(2,'DB_CONFIG2')->query($sql);
        return $res;
    }

    public function cxygqx($yggh){
        $sql="select xg,qxmc from appqxda where bmmc in(select bm from daygda where yggh='$yggh')";
        $res = $this->db(2,'DB_CONFIG2')->query($sql);
        return $res;


    }

}