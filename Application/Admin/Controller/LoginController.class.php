<?php
/**
 * Created by PhpStorm.
 * User: stevehum
 * Date: 17/8/13
 * Time: 15:28
 */

namespace Admin\Controller;


use Think\Controller;

class LoginController extends Controller {
    public function login(){
        if(IS_POST)
        {
            $adminModel = D('Admin');
            if($adminModel->validate($adminModel->_login_check)->create()){
                if($adminModel->login()){
                    $this->success('登陆成功！',U('Index/index'));
                    exit;

                }
            }
            $this->error($adminModel->getError());

        }
        $this->display();
    }
    public function logout()
    {
        $adminModel = D('Admin');
        $adminModel->logout();
        redirect(U('Login/login'));
    }

}