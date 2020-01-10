<?php
/**
 * Created by PhpStorm.
 * User.class: Administrator
 * Date: 2019-11-05
 * Time: 15:18
 */

namespace Home\Entity;


class User
{
    public function hqdlxx($dh){
        $sql="select * from user where dh='$dh' and zt=1";
        $res = M()->db(1,'DB_CONFIG1')->query($sql);
        return $res;
    }

    public function yzdl($qxkh){
        $sql="select * from daygda where  qxkh='$qxkh'";
        $res =M()->db(2,'DB_CONFIG2')->query($sql);
        return $res;
    }

    public function cxbmxx($zh){
        $sql="select bm from dabmda where bm=(select bm from daygda where qxkh='$zh')";
        $res =M()->db(2,'DB_CONFIG2')->query($sql);
        return $res;

    }

    public function cxbmqx($bm,$dh){
        $sql="select bm from dabmda where bm='$bm'";
        $res = M()->db(1,'DB_CONFIG1')->query($sql);
        if(empty($res)){
            $sql="insert into dabmda(bm) select '$bm'";
            M()->db(1,'DB_CONFIG1')->execute($sql);
        }
        $sql="select qxmc from dabmda where bm='$bm'and dh='$dh' ";
        $res = M()->db(1,'DB_CONFIG1')->query($sql);
        return $res;
    }

    public function xgmm($yggh,$mm){
        $sql="update daygda set mm='$mm' where yggh='$yggh' ";
        $res = M()->execute($sql);
        return $res;
    }

}