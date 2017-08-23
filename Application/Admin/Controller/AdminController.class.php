<?php
/**
 * Created by PhpStorm.
 * User: stevehum
 * Date: 17/8/12
 * Time: 16:13
 */

namespace Admin\Controller;


use Think\Controller;

class AdminController extends BaseController {
    /*
     *添加管理员
     */
    public function add(){
        $adminModel = D('Admin');
        if(IS_POST){
            if($adminModel->create(I('post.'),1))
            {
                if($adminModel->add())
                {
                    $this->success('添加成功！',U('lst?p='.I('get.p')));
                    exit;
                }
            }
            $this->error($adminModel->getError());
        }
        //获取所有角色
        $roleModel = D('Role');
        $roleData = $roleModel->where(array('status'=>1))->select();
        $this->assign(array(
            'roleData'=>$roleData,
            '_page_title'=>'添加管理员',
            '_page_btn_name'=>'管理员列表',
            '_page_btn_url'=>U('lst')
        ));
        $this->display();
    }
    /*
     *编辑管理员
     */
    public function edit(){
        $adminModel = D('Admin');
        $id = I('param.id');
        if(empty($id)){
            $this->error('没有获取到用户ID');

        }
        if(IS_POST){
            if($adminModel->create(I('post.'),2))
            {
                if($adminModel->save() !== FALSE)
                {
                    $this->success('修改成功！',U('lst',array('p'=>I('get.p',1))));
                    exit;
                }
            }
            $this->error($adminModel->getError());

        }


        //获取管理员信息
        $data = $adminModel->where(array('id'=>$id))->find();
        //获取管理员所拥有的角色
        $raData = M('RoleAdmin')->where(array('admin_id'=>$id))->select();
        $arr = array();
        foreach($raData as $v)
        {
            $arr[] = $v['role_id'];
        }

        //获取所有角色
        $roleModel = D('Role');
        $roleData = $roleModel->where(array('status'=>1))->select();
        $this->assign(array(
            'roleData'=>$roleData,
            'arr'=>$arr,
            'data'=>$data,
            '_page_title'=>'编辑管理员',
            '_page_btn_name'=>'管理员列表',
            '_page_btn_url'=>U('lst')
        ));
        $this->display();
    }
    /*
     * 显示管理员列表
     */
    public function lst(){
        //获取所有管理员
        $adminModel = D('Admin');
        $data  = $adminModel->search();
        $this->assign(array(
            'data'=>$data['data'],
            'page'=>$data['page'],
            '_page_title'=>'管理员列表',
            '_page_btn_name'=>'添加管理员',
            '_page_btn_url'=>U('add')
        ));
        $this->display();
    }
    public function delete(){
        $id = I('get.id');
        if(empty($id))
            $this->error("没有获取到ID！");
        $adminModel = D('Admin');
        if($adminModel->delete($id)){
            $this->success("删除成功！",U('lst'));
            exit;
        }
        $this->error($adminModel->getError());
    }

}