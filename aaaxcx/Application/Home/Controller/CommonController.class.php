<?php
/**
 * Created by PhpStorm.
 * User.class: Administrator
 * Date: 2019-10-31
 * Time: 9:10
 */

namespace Home\Controller;


use Think\Controller;

class CommonController extends Controller
{
    public function __construct()
    {
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Methods:*');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        header('Access-Control-Allow-Methods:GET, POST, OPTIONS');
        parent::__construct();
        if ($_COOKIE['PHPSESSID']) {
            session_id($_COOKIE['PHPSESSID']);
        }
        $config = array(
            'DB_TYPE' => 'sqlsrv',
            'DB_HOST' => $_SESSION['arr']['ym'],
            'DB_NAME' => $_SESSION['arr']['sjkmc'],
            'DB_USER' => $_SESSION['arr']['yh'],
            'DB_PWD' =>  $_SESSION['arr']['sjkmm'],
            'DB_CHARSET' => 'utf8',
            'URL_MODEL' => 1,
        );
        C($config);
    }

}