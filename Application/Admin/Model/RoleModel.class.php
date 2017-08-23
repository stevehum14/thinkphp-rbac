<?php
/**
 * Created by PhpStorm.
 * User: stevehum
 * Date: 17/8/11
 * Time: 20:29
 */

namespace Admin\Model;
use Think\Model;
use Think\Think;

class RoleModel extends Model {
    protected $insertFields = array('role_name','status','pri_id');
    protected $updateFields = array('id','role_name','status','pri_id');
    protected $_validate = array(
        array('role_name','require','角色名称不能为空！',1,'regex',3),
        array('role_name','1,30','角色名称最大长度不能超过30个字节！',1,'length',3),
        array('role_name','','该角色名称已经存在！',1,'unique',1),
        array('role_name','require','角色名称不能为空！',1,'regex',3),
    );
    protected function _before_insert(&$data,$option){
        $status = I('post.status');
        if(empty($status))
            $data['status']= 1;
    }
    protected function _after_insert($data,$option){
        $pri_id =I('post.pri_id');
        if($pri_id)
        {
            $prModel = D('PriRole');
            //循环插入中间表
            foreach($pri_id as $k=>$v){
                $prModel->add(array(
                    'pri_id'=>$v,
                    'role_id'=>$data['id']
                ));
            }
        }

    }
    protected function _before_update($data,$option){
        $roleId = $option['where']['id'];
        //先删除与该角色相关联的表数据
        $prModel = D('PriRole');
        $prModel->where(array('role_id'=>$roleId))->delete();
        $pri_id =I('post.pri_id');
        if($pri_id)
        {
            $prModel = D('PriRole');
            //循环插入中间表
            foreach($pri_id as $k=>$v){
                $prModel->add(array(
                    'pri_id'=>$v,
                    'role_id'=>$roleId
                ));
            }
        }

    }
    protected function _before_delete($option){
        $id = $option['where']['id'];
        //删除与角色相关的表数据
        $prModel = D('PriRole');
        $prModel->where(array('role_id'=>$id))->delete();
        $raModel = D('RoleAdmin');
        $raModel->where(array('role_id'=>$id))->delete();

    }
    public function search($pageSize = 20){
        $where = array();
        $count = $this->alias('a')->where($where)->count();
        $page = new \Think\Page($count, $pageSize);
        $page->setConfig('next','下一页');
        $page->setConfig('prev','上一页');
        $data['page']=$page->show();
        $data['data'] = $this->field('a.*,GROUP_CONCAT(c.name) pri_name')->alias('a')->where($where)
            ->join('LEFT JOIN __PRI_ROLE__ b ON a.id = b.role_id')
            -> join('LEFT JOIN __PRIVILEGE__ c ON b.pri_id = c.id')
            ->limit($page->firstRow.','.$page->listRows)
            ->group('a.id')
            ->select();
        return $data;

    }


}