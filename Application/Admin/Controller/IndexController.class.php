<?php
/**
 * Created by PhpStorm.
 * User: stevehum
 * Date: 17/8/11
 * Time: 00:53
 */
namespace Admin\Controller;
use Think\Controller;

class IndexController extends BaseController {
    public function index()
    {
        $priModel = D('privilege');
        //$btns = $priModel->getPrivilege();
       $this->assign(array(
           '_page_title'=>'首页'
       ));
       $this->display();
    }
    public function menu(){
        $this->display();
    }
    public function top(){
        $this->display();
    }
    public function main(){
        $this->display();
    }

}