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
		$this->_layout->setLayoutFile('layout.html');
	}
	/**
	 * 用户添加
	 */
	public function AddAction()
	{
		if($_POST){
			$Posts = $this->getRequest()->getPost();
			$Posts['passwd'] = md5($Posts['passwd']);
			$Posts['time'] = date("Y-m-d H:i:s");
			$Posts['lastime'] = date("Y-m-d H:i:s");
			foreach($Posts as $v){
				if(empty($v)){
					echo "<script>alert('请正确填写，不能为空！')</script>";exit;
				}
			}
			if($this->Usr->AddUsr($Posts)){
				echo "<script>location.href='/usr/index/'</script>";exit;
			}else{
				echo "<script>alert('添加失败');</script>";exit;
			}
		}else{
			$this->_layout->setLayoutFile('layout.html');
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
			foreach($Posts as $k => $v)
			{
				if(empty($v)){
					unset($Posts[$k]);
				}
			}
			if($this->Usr->EditUsr($id,$Posts)){
				echo "<script>alert('修改成功');location.href='/usr/edit/?id={$id}';</script>";exit;
			}else{
				echo "<script>alert('修改失败');</script>";exit;
			}
		}
		//获取用户信息
		$id = $this->getRequest()->getQuery('id');
		//获取用户信息
		$UsrInfo = $this->Usr->GetUsrInfo($id);
		$channels = $this->_config->app->usechannel->toArray();
		$this->_view->UsrInfo = $UsrInfo;
		$this->_view->channels = $channels;
		$this->_layout->meta_title = '用户编辑';
		$this->_layout->setLayoutFile('layout.html');		
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
		$this->_layout->setLayoutFile('layout.html');
	}
}