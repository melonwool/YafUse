<?php
class SearchController extends Controller{
	public function init(){
		parent::init();	
	}

	public function IndexAction(){	
		$this->_view->id = $this->_req->getParam('id');
		$this->_view->code =  $this->_req->getParam('code');
		$this->_view->key =  $this->_req->getParam('key');
	}
}
