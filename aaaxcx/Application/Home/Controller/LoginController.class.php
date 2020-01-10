<?php


namespace Home\Controller;
header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Methods:*');
header('Access-Control-Allow-Headers:x-requested-with,content-type');
header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
use Home\Entity\User;
use Think\Controller;

class LoginController extends Controller
{
    public function login()
    {
        $zh = I('kh');
       // $zh = substr($zh,4);
        $dh = I('dh');
        $user = new User();
        $res = $user->hqdlxx($dh);
        $yh = $res[0]['sjkyh'];
        $sjkmm = $res[0]['sjkmm'];
        $sjkmc = $res[0]['sjkmc'];
        $qx = $res[0]['qxmc'];
        $ym = $res[0]['ym'];
        $str = "sqlsrv://" . $yh . ":" . $sjkmm . "@" .$ym."/".$sjkmc;
        C('DB_CONFIG2', $str);
        $res = $user->yzdl($zh);
        $bms = $user->cxbmxx($zh);
        $bm = $bms[0]['bm'];
        $bmqx = $user->cxbmqx($bm,$dh);
        if (!empty($res)) {
            $json['status'] = 1;
            $arr = array(
                'yh'=>$yh,'sjkmc'=>$sjkmc,'sjkmm'=>$sjkmm,'ym'=>$ym
            );
            session_start();
            session('arr',$arr);
            $json['mm'] = $res[0]['mm'];
            $json['yggh'] = $res[0]['yggh'];
            $json['ygmc'] = $res[0]['ygmc'];
            $json['qx'] = $qx;
            $json['bmqx'] = $bmqx;
            $this->ajaxReturn($json, 'json');


        }


    }

    public function cxdh($user, $dh)
    {
        $res = $user->hqdlxx($dh);
        if (!empty($res)) {
            $ym = $res[0]['ym'];
            $sjkmc = $res[0]['sjkmc'];
            $sjkmm = $res[0]['sjkmm'];
            $sjkyh = $res[0]['sjkyh'];
            $config = array(
                'DB_TYPE' => 'sqlsrv',
                'DB_HOST' => $ym,
                'DB_NAME' => $sjkmc,
                'DB_USER' => $sjkyh,
                'DB_PWD' => $sjkmm,
                'DB_CHARSET' => 'utf8',
            );
            C($config);
            //   $json['status'] = 1;
            // $this->ajaxReturn($json, 'json');

        } /*else {
            $json['status'] = 0;
            $this->ajaxReturn($json, 'json');

        }*/
    }


}