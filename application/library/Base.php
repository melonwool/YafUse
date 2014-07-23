<?php
class Base extends Yaf_Controller_Abstract {

	public function init(){
		$this->_config = Yaf_Registry::get('config');
		$this->_req = $this->getRequest();
		$this->_session = Yaf_Session::getInstance();
		$this->_session->start();
		if(!$this->_session->has('username')){
			$this->redirect('/index/');
		}
	}
}
