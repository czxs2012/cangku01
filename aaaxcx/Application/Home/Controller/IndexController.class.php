<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;
use Home\Entity\User;
class IndexController extends CommonController {
    public function index(){
        $this->display();
    }

    public function xgmm(){
        $yggh = I('yggh');
        $mm = I('mm');
        $user = new User();
        $res = $user->xgmm($yggh,$mm);
        if($res){
            $json['status']=1;
            $this->ajaxReturn($json,'json');
        }


    }


}