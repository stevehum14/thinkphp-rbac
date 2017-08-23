<?php
/**
 * Created by PhpStorm.
 * User: stevehum
 * Date: 17/8/13
 * Time: 16:40
 */

namespace Admin\Controller;


use Think\Controller;

class BaseController extends Controller {
    public function __construct(){
        parent::__construct();
        //判断登陆
        if(!session('id')){
            $this->error('请先登陆！',U('Login/login'));
            exit;
        }
        if(CONTROLLER_NAME == 'Index'){
            return true;
        }
        //判断管理员拥有的权限
        $priModel = D('Privilege');
        if(!$priModel->chkPri()){
            $this->error("没有权限访问");
        }
    }

}