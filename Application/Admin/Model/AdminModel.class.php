<?php
/**
 * Created by PhpStorm.
 * User: stevehum
 * Date: 17/8/12
 * Time: 17:04
 */

namespace Admin\Model;


use Think\Model;

class AdminModel extends Model {
    protected $insertFields = array('username','password','cpassword','status','role_id');
    protected $updateFields = array('id','username','password','cpassword','status','role_id');
    protected $_validate = array(
        array('username','require','必须输入用户名！',1,'regex',3),
        array('username','1,30','用户名长度不能超过30个字节！',1,'length',3),
        array('password','require','必须输入密码！',1,'regex',1),
        array('cpassword','require','必须输入确认密码！',1,'regex',1),
        array('cpassword','password','确认密码不正确！',1,'confirm',1),
    );
    protected function _before_insert(&$data,$option){
        $data['password'] = md5($data['password']);

    }
    protected function _after_insert($data,$option){
        $roleId = I('post.role_id');
        if($roleId){
            $raModel = D('RoleAdmin');
            foreach($roleId as $v){
                $raModel->add(array(
                    'role_id'=>$v,
                    'admin_id'=>$data['id']
                ));
            }
        }
    }
    public  $_login_check = array(
        array('username','require','必须输入用户名！',1,'regex',3),
        array('password','require','必须输入密码！',1,'regex',3),
    );
    /*
     * 登陆
     */
    public function login(){
        $username = I('post.username');
        $password = I('post.password');
         $data= $this->where(array('username'=>$username))->find();
        if(empty($data)){
            $this->error="用户不存在！";
            return false;
        }
        if($data['status'] != 1){
            $this->error="用户已被禁用！";
            return false;
        }
        if(md5($password) !== $data['password']){
            $this->error="密码错误！";
            return false;
        }
        session('id',$data['id']);
        session('username',$data['username']);
        return true;
    }
    protected function _before_update(&$data,$option){
        $id = $option['where']['id'];
        $raModel = D('RoleAdmin');
        $raModel->where(array('admin_id'=>$id))->delete();//先删除role_admin表中的数据
        //添加数据
        $roleId = I('post.role_id');
        if($roleId)
        {
            foreach($roleId as $v)
            {
                $raModel->add(array(
                    'role_id'=>$v,
                    'admin_id'=>$id
                ));
            }
        }

        if($data['password'])
            $data['password'] = md5($data['password']);
        else
            unset($data['password']);


    }
    public function search($pageSize = 20){
        $where = array();
        $count = $this->alias('a')->where($where)->count();
        $page = new \Think\Page($count,$pageSize);
        $page->setConfig('next','');
        $page->setConfig('next','');
        $data['page'] = $page->show();
        $data['data'] = $this->alias('a')->field('a.id,a.username,a.status,GROUP_CONCAT(c.role_name) role_name')
            ->where($where)
            ->join('LEFT JOIN __ROLE_ADMIN__ b ON a.id = b.admin_id')
            ->join('LEFT JOIN __ROLE__ c ON b.role_id = c.id')
            ->limit($page->firstRow.','.$page->listRows)
            ->group('a.id')
            ->select();
        return $data;

    }
    protected function _before_delete($option){

        $id = $option['where']['id'];
        if($id == 1){
            $this->error="超级管理员无法删除！";
            return false;
        }
        //删除与该管理员相关的表数据
        $raModel = D('RoleAdmin');
        $raModel->where(array('admin_id'=>$id))->delete();

    }
    public function logout()
    {
        session(null);
    }


}