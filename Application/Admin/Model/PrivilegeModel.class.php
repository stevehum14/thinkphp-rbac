<?php
/**
 * Created by PhpStorm.
 * User: stevehum
 * Date: 17/8/11
 * Time: 17:40
 */

namespace Admin\Model;


use Think\Model;

class PrivilegeModel extends Model {
    protected $insertFields = array('name','module_name','controller_name','action_name','parent_id','status');
    protected $updateFields = array('id','name','module_name','controller_name','action_name','parent_id','status');
    protected $_validate = array(
        array('name','require','权限名称不能为空！',1,'regex',3),
        array('name','1,30','权限名称长度不能超过30字节！',1,'length',3),
        array('name','','权限名称已经存在！',1,'unique',1),//新增时验证
        array('module_name','1,30','模块名称长度不能超过30字节！',2,'length',3),
        array('controller_name','1,30','控制器名称长度不能超过30字节！',2,'length',3),
        array('action_name','1,30','方法名称长度不能超过30字节！',2,'length',3),
    );
    protected function _before_insert(&$data,$option){
        $status = I('post.status');
        if(empty($status))
            $data['status']= 1;
    }

    /*
     * 获取权限属性表现形式
     */
    public function getTree(){
        $priData = $this->select();
        return $this->_getTree($priData);
    }
    protected function _getTree($data,$parentId = 0,$level = 0,$isClear = TRUE){
       static $ret = array();
        if($isClear)
            $ret = array();
        foreach($data as $k=>$v){
            if($v['parent_id'] == $parentId){
                $v['level'] = $level;
                $ret[] = $v;
                $this->_getTree($data,$v['id'],$level + 1,false);
            }
        }
        return $ret;

    }
    public function getChildren($id){
        $data = $this->select();
        return $this->_getChildren($data,$id);

    }
    protected function _getChildren($data,$parentId = 0,$isClear = true){
        static $ret = array();
        if($isClear)
            $ret = array();
        foreach($data as $v){
            if($v['parent_id'] == $parentId){
                $ret[] = $v['id'];
                $this->_getChildren($data,$v['id'],FALSE);
            }
        }
        return $ret;

    }
    protected function _before_delete(&$option){

        $id = $option['where']['id'];
        //获取所有子类权限
        $children = $this->getChildren($id);
        $children[] = $id;

        //删除与该权限相关的表数据
        $prModel = D('PriRole');
        $prModel->where(array('pri_id'=>array('in',implode(',',$children))))->delete();
        $option['where']['id'] = array('IN',implode(',',$children));

    }
    public function chkPri(){
        $id = session('id');
        //如果是超级管理员则返回
        if($id == 1)
            return true;
        $raModel = D('RoleAdmin');
        $has = $raModel->alias('a')
            ->join('LEFT JOIN __PRI_ROLE__ b ON a.role_id = b.role_id')
            ->join('LEFT JOIN __PRIVILEGE__ c ON b.pri_id = c.id')
            ->where(array(
                'a.admin_id'=>$id,
                'c.module_name'=>strtolower(MODULE_NAME),
                'c.controller_name'=>strtolower(CONTROLLER_NAME),
                'c.action_name'=>strtolower(ACTION_NAME),
            ))->COUNT();
        return ($has > 0);
    }

    /*
     * 获取当天管理者的两级权限
     */
    public function getPrivilege()
    {
        $id = session('id');
        if($id == 1)
        {
            $pris = $this->select();
        }else{
            $raModel = D('RoleAdmin');
            $pris =  $raModel->alias('a')
                ->field('DISTINCT c.id,c.name,c.module_name,c.controller_name,c.action_name,c.parent_id')
                ->join('LEFT JOIN __PRI_ROLE__ b ON a.role_id = b.role_id')
                ->join('LEFT JOIN __PRIVILEGE__ c ON b.pri_id = c.id')
                ->where(array('a.admin_id'=>$id))->select();
        }
        $arr = array();
        foreach($pris as $v)
        {


            if($v['parent_id'] == 0)
            {
                foreach($pris as $c)
                {

                    if($c['parent_id'] == $v['id'])
                    {
                        $v['children'][] = $c;
                    }
                }
                $arr[] = $v;
            }
        }
        return $arr;


    }



}