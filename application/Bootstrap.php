<?php
class Bootstrap extends Yaf_Bootstrap_Abstract {

    private $_config;

    /*get a copy of the config*/
    public function _initBootstrap(){
        $this->_config = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $this->_config);
    }

    public function _initIncludePath(){
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->_config->application->library);
    }
	/*
    public function _initErrors(){
        if($this->_config->application->showErrors){
            error_reporting (-1);
            //报错是否开启
            ini_set('display_errors','On');
            set_error_handler('handleError', E_ALL);
        }else{
            error_reporting (-1);
            set_error_handler('handleError', E_ALL);
        }
	}*/
    /**
     * [路由设置]
     */
    public function _initRoutes(){
		$router = Yaf_Dispatcher::getInstance()->getRouter();
		//载入config中路由
        $router->addConfig(Yaf_Registry::get('config')->routes);
    }
    /**
     * layout页面布局
     */
    public function _initLayout(Yaf_Dispatcher $dispatcher){
        Yaf_Registry::set('dispatcher', $dispatcher);
    }
}
