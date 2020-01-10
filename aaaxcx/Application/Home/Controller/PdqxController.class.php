<?php
/**
 * Created by PhpStorm.
 * User.class: Administrator
 * Date: 2019-11-01
 * Time: 17:25
 */

namespace Home\Controller;


class PdqxController extends CommonController
{
    public function pdqx(){
        $zw = I('zw');
        if($zw=='老板'||$zw=='管工'){
            $json['status']=1;
            $this->ajaxReturn($json,'json');
        }
    }


}