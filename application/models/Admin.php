<?php
class AdminModel extends Db_Base{
	protected $_table = "admin";
	/**
	 * 用户登录判断
	 */
	public function LoginUsr($usr, $passwd)
	{
		$usrfingo = $this->_db->selectFirst($this->_table,array('username'=>$usr,'password'=>$passwd));
		//$usrinfo = $this->_db->select($this->_table,array('username'=>$usr,'password'=>$passwd),1);
		//可以用query方法
		//$sql = "SELECT * FROM $this->_table WHERE username='{$usr}' AND password='{$passwd}' AND is_del='0'";
		//$usrinfo = $this->_db->query($sql);
		if($usrinfo){
			return $usrinfo;
		}else{
			return false;
		}
	}
	/**
	 * 显示所有用户信息
	 */
	public function ShowUsers()
	{
		//$sql = "SELECT * FROM $this->_table WHERE is_del='0'";
		//return $this->_db->query($sql);
		$usrinfo = $this->_db->select($this->_table,array('is_del'=>'0'));		
		return $usrinfo;
	}
	/**
	 * 
	 * 添加用户
	 * @param [type] $info [description]
	 */
	public function AddUsr($info)
	{
		if($this->_db->insert($this->_table,$info)){
			return true;
		}else{
			return false;
		}
	}
	/**
	 *根据id获取用户信息
	 * @param [type] $id [description]
	 */
	public function GetUsrInfo($id)
	{
		$sql = "SELECT * FROM $this->_table WHERE id=$id ";
		$this->_db = new Db_Mysql ($this->_config->database->config->toArray());
		$usrinfo = $this->_db->query($sql);
		return $usrinfo[0];
	}
	/**
	 * 编辑用户
	 * @param [type] $id     [description]
	 * @param [type] $params [description]
	 */
	public function EditUsr($id,$params)
	{
		$this->_db = new Db_Mysql ($this->_config->database->config->toArray());
		$wheres = array('id'=>$id);
		if($this->_db->update($this->_table, $params, $wheres)){
			return true;
		}else{
			return false;
		}
	}
	/**
	 * 删除用户
	 * @param [type] $id [description]
	 */
	public function Del($id)
	{
		$sql = "DELECT FROM $this->_table WHERE id='{$id}' ";
		$params = array('is_del'=>1);
		$wheres = array('id'=>$id);
		$this->_db = new Db_Mysql ($this->_config->database->config->toArray());
		if($this->_db->update($this->_table, $params, $wheres)){
			return true;
		}else{	
			return false;
		}
	}	
}