<?php
/**
 * Created by PhpStorm.
 * User: stevehum
 * Date: 17/8/11
 * Time: 20:27
 */

namespace Admin\Controller;


use Think\Controller;

class RoleController extends BaseController {
    /*
    * 添加权限
    */
    public function add(){

        if(IS_POST){
            $roleModel = D('Role');
            if($roleModel->create(I('post.'),1))
            {

                if($roleModel->add()){
                    $this->success('添加成功！',U('lst?p='.I('get.p')));
                    exit;
                }
            }
            $this->error($roleModel->getError());
        }
        $priModel = D('Privilege');
        //获取全部权限
        $priData = $priModel->getTree();
        $this->assign(array(
            'priData'=>$priData,
            '_page_title'=>'添加角色',
            '_page_btn_name'=>'角色列表',
            '_page_btn_url'=>U('lst')
        ));

        $this->display();
    }
    /*
     * 编辑权限
     */
    public function edit(){
        $roleModel = D('Role');
        if(IS_POST){
            if($roleModel->create(I('post.'),2)){
                if($roleModel->save() !== FALSE){
                    $this->success('修改成功！',U('lst',array('p'=>I('get.p'))));
                    exit;
                }
            }
            $this->error($roleModel->getError());

        }

        $id = I('get.id');
        $data = $roleModel->where(array('id'=>$id))->find();

        //获取角色拥有的权限
        $prData = M('PriRole')->where(array('role_id'=>$id))->select();
        $arr = array();
        foreach($prData as $v){
            $arr[] = $v['pri_id'];
        }

        //获取全部权限
        $priModel = D('Privilege');
        $priData = $priModel->getTree();
        $this->assign(array(
            'arr'=>$arr,
            'data'=>$data,
            'priData'=>$priData,
            '_page_title'=>'编辑角色',
            '_page_btn_name'=>'角色列表',
            '_page_btn_url'=>U('lst')
        ));
        $this->display();
    }
    /**
     * 权限列表
     */
    public function lst(){
        $roleModel = D('Role');
        $data  = $roleModel->search();
        $this->assign(array(
            'data'=>$data['data'],
            'page'=>$data['page'],
            '_page_title'=>'角色列表',
            '_page_btn_name'=>'添加角色',
            '_page_btn_url'=>U('add')
        ));
        $this->display();
    }
    /*
     * 删除角色
     */
    public function delete(){
        $id = I('get.id');
        if(empty($id))
            $this->error("没有获取到ID！");
        $roleModel = D('Role');
        if($roleModel->delete($id)){
            $this->success("删除成功！",U('lst'));
            exit;
        }
        $this->error($roleModel->getError());
    }

}