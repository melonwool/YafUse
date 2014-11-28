<?php

class AdminController extends Controller_Base {
    private $_layout;
    private $_Admin;
    public function init() {
        parent::init();
        //使用layout页面布局
        $this->_layout = new LayoutPlugin('layout.html');
        $this->dispatcher = Yaf_Registry::get("dispatcher");
        $this->dispatcher->registerPlugin($this->_layout);
        $this->_Admin = new AdminModel();
    }
    /**
     * 用户管理首页
     */
    public function IndexAction() {
        //获取用户信息
        $User_info = $this->_Admin->ShowUsers();
        $this->_view->usr_info = $User_info;
        $this->_layout->admin = true;
    }
    /**
     * 用户添加
     */
    public function AddAction() {
        if ($this->_req->isXmlHttpRequest()) {
            $Posts = $this->_req->getPost();
            $Posts['password'] = md5($Posts['password']);
            $Posts['repassword'] = md5($Posts['repassword']);
            
            foreach ($Posts as $v) {
                if (empty($v)) {
                    $this->show_json(array('errno'=>0,'errmsg'=>'请正确填写，不能为空哦!'));
                }
            }
            if ($Posts['password'] != $Posts['repassword']) {
                $this->show_json(array('errno'=>0,'errmsg'=>'两次密码输入不一致!'));
            }
            unset($Posts['repassword']);
            $Posts['is_del'] = 0;
            if ($this->_Admin->AddUsr($Posts)) {
                $this->show_json(array('errno'=>0,'errmsg'=>'添加成功!'));
            } else {
                $this->show_json(array('errno'=>0,'errmsg'=>'添加失败!'));
            }
        } else {
            $this->_layout->add = true;
        }
    }
    /**
     * 编辑用户信息
     */
    public function EditAction() {
        if ($this->_req->isXmlHttpRequest()) {
            $Posts = $this->_req->getPost();
            $id = $Posts['id'];
            unset($Posts['id']);
            $Posts['password'] = md5($Posts['password']);
            $Posts['repassword'] = md5($Posts['repassword']);
            if ($Posts['password'] != $Posts['repassword']) {
                $this->show_json(array('errno'=>0,'errmsg'=>'两次密码输入不一致!'));
            }
            
            foreach ($Posts as $k => $v) {
                if ($k == 'password' || $k == 'repassword') {
                    unset($Posts[$k]);
                } else {
                    if (empty($v)) {
                        $this->show_json(array('errno'=>0,'errmsg'=>'请正确填写，不能为空哦!'));
                    }
                }
            }
            if ($this->_Admin->EditUsr($id, $Posts)) {
                $this->show_json(array('errno'=>0,'errmsg'=>'修改成功'));
            } else {
                $this->show_json(array('errno'=>0,'errmsg'=>'修改失败'));
            }
        }
        //获取用户信息
        $id = $this->_req->getQuery('id');
        //获取用户信息
        $UsrInfo = $this->_Admin->GetUsrInfo($id);
        $this->_view->UsrInfo = $UsrInfo;
    }
    /**
     * 删除用户
     */
    public function DelAction() {
        $id = $this->_req->getPost('id');
        if ($this->_Admin->Del($id)) {
            $this->show_json(array('errno'=>0,'errmsg'=>'删除成功'));
        } else {
            $this->show_json(array('errno'=>101,'errmsg'=>'删除失败'));
        }
    }
    /**
     * 用户修改密码
     */
    public function PasswdAction() {
        $this->_layout->meta_title = '修改密码';
    }
}
