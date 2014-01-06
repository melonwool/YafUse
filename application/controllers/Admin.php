<?php
class AdminController extends Yaf_Controller_Abstract {

	private $_layout;
	public function init(){
		//使用layout页面布局
		$this->_layout = Yaf_Registry::get('layout');
		$this->_config = Yaf_Registry::get('config');
		$this->_Admin = new AdminModel();
	}
	/**
	 * 用户管理首页
	 */
	public function IndexAction()
	{
		//获取用户信息
		$User_info = $this->_Admin->ShowUsers();
		$this->_view->usr_info = $User_info;
		$this->_layout->admin = true;
	}
	/**
	 * 用户添加
	 */
	public function AddAction()
	{
		if($_POST){
			$Posts = $this->getRequest()->getPost();
			$Posts['password'] = md5($Posts['password']);
			$Posts['repassword'] = md5($Posts['repassword']);
			foreach($Posts as $v){
				if(empty($v)){
					exit("101:请正确填写，不能为空哦！");
				}
			}
			if($Posts['password']!=$Posts['repassword']){
				exit("102:两次密码输入不一致!");
			}
			unset($Posts['repassword']);
			$Posts['is_del'] = 0;
			if($this->_Admin->AddUsr($Posts)){
				exit("100:添加成功！");
			}else{
				exit("103:添加失败!");
			}
		}else{
			$this->_layout->add = true;
		}
	}
	/**
	 * 编辑用户信息
	 */
	public function EditAction()
	{
		if($_POST){
			$Posts = $this->getRequest()->getPost();
			$id = $Posts['id'];
			unset($Posts['id']);
			$Posts['password'] = md5($Posts['password']);
			$Posts['repassword'] = md5($Posts['repassword']);
			if($Posts['password']!=$Posts['repassword']){
				exit("102:两次密码输入不一致!");
			}
			foreach($Posts as $k=>$v){
				if($k=='password' || $k=='repassword'){
					unset($Posts[$k]);
				}else{
					if(empty($v)){
						exit("101:请正确填写，不能为空哦！");
					}
				}
			}
			if($this->_Admin->EditUsr($id,$Posts)){
				exit("100:修改成功!");
			}else{
				exit("103:修改失败!");
			}
		}
		//获取用户信息
		$id = $this->getRequest()->getQuery('id');
		//获取用户信息
		$UsrInfo = $this->_Admin->GetUsrInfo($id);
		$this->_view->UsrInfo = $UsrInfo;
	}
	/**
	 * 删除用户
	 */
	public function DelAction(){
		$id = $this->getRequest()->getPost('id');
		if($this->Usr->Del($id)){
			echo 1;exit;
		}else{
			echo 2;exit;
		}
	}
	/**
	 * 用户修改密码
	 */
	public function PasswdAction()
	{
		$this->_layout->meta_title = '修改密码';
	}
}