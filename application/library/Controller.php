<?php
class Controller extends Yaf_Controller_Abstract{
	public function init(){
		$this->_req = $this->getRequest();	
	}
}
