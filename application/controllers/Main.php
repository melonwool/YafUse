<?php
class MainController extends Controller_Base {
    private $_layout;

    public function init(){
		parent::init();
        $this->_layout = new LayoutPlugin('layout.html');
        $this->dispatcher = Yaf_Registry::get("dispatcher");
        $this->dispatcher->registerPlugin($this->_layout);
    }

    public function indexAction()
    {
    }
}

