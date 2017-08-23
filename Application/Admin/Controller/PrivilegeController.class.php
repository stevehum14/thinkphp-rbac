<?php
/**
 * Created by PhpStorm.
 * User: stevehum
 * Date: 17/8/11
 * Time: 02:43
 */

namespace Admin\Controller;


use Think\Controller;

class PrivilegeController extends BaseController {
    /*
     * 添加权限
     */
    public function add(){
        $priModel = D('Privilege');
        if(IS_POST){
            //dump(I('post.'));exit;
            if($priModel->create(I('post.'),1))
            {
                if($priModel->add()){
                    $this->success('添加成功！',U('lst?p='.I('get.p')));
                    exit;
                }
            }
            $this->error($priModel->getError());
        }
        $this->assign(array(
            '_page_title'=>'添加权限',
            '_page_btn_name'=>'权限列表',
            '_page_btn_url'=>U('lst')
        ));
        //获取全部权限
        $priData = $priModel->getTree();
        $this->assign(array(
            'priData'=>$priData
        ));
        $this->display();
    }
    /*
     * 编辑权限
     */
    public function edit(){
        $priModel = D('Privilege');
        if(IS_POST){
            if($priModel->create(I('post.'),2)){
                if($priModel->save() !== FALSE){
                    $this->success('修改成功！',U('lst',array('p'=>I('get.p'))));
                    exit;
                }
            }
            $this->error($priModel->getError());

        }
        $id = I('get.id');
        $data = $priModel->where(array('id'=>$id))->find();
        //获取全部权限
        $priData = $priModel->getTree();
        $this->assign(array(
            'data'=>$data,
            'priData'=>$priData,
            '_page_title'=>'编辑权限',
            '_page_btn_name'=>'权限列表',
            '_page_btn_url'=>U('lst')
        ));
        $this->display();
    }
    /**
     * 权限列表
     */
    public function lst(){
        $priModel = D('Privilege');
        $priData = $priModel->getTree();
        $this->assign(array(
            'priData'=>$priData,
            '_page_title'=>'权限列表',
            '_page_btn_name'=>'添加权限',
            '_page_btn_url'=>U('add')
        ));
        $this->display();
    }
    public function delete(){
        $id = I('get.id');
        if(empty($id))
        {
            $this->error("没有获取到ID");
        }
        $priModel = D('Privilege');
        if($priModel->delete($id)){
            $this->success('删除成功！',U('lst'));
            exit;
        }
        $this->error($priModel->getError());

    }


}