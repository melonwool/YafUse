<?php
class MainController extends Yaf_Controller_Abstract {
    private $_layout;

    public function init(){
        //使用layout页面布局
        $this->_layout = new LayoutPlugin('layout.html');
        $this->dispatcher = Yaf_Registry::get("dispatcher");
        $this->dispatcher->registerPlugin($this->_layout);
    }

    public function indexAction()
    {
    	if(!isset($_SESSION['username'])){
    		header('Location:/index/');
    	}
    }
}

