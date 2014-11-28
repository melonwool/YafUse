<?php
class Bootstrap extends Yaf_Bootstrap_Abstract {
    private $_config;
    /**
     * [初始化 配置信息]
     * @return [type] [description]
     */
    public function _initBootstrap() {
        $this->_config = Yaf_Application::app()->getConfig();
        Yaf_Registry::set('config', $this->_config);
    }
    /**
     * [加载 命名空间 加载local library components文件]
     * @return [type] [description]
     */
    public function _initRegisterLocalNamespace()
    {
        $loader = Yaf_Loader::getInstance();
        $loader->registerLocalNamespace(
            array('Controller','Helper')
        );
    }

    /**
     * [默认视图类(报错已用)]
     * @param  Yaf_Dispatcher $dispatcher [description]
     * @return [type]                     [description]
     */
    public function _initView(Yaf_Dispatcher $dispatcher) {
        $dispatcher->setView(new View(null));
    }
    /**
     * [错误处理]
     * @return [type] [description]
     */
    public function _initErrors() {
        //报错是否开启
        if ($this->_config->application->showErrors) {
            error_reporting(-1);
            ini_set('display_errors', 'On');
        } else {
            error_reporting(0);
            ini_set('display_errors', 'Off');
        }
        set_error_handler(['Error', 'errorHandler']);
    }
    /**
     * [路由设置]
     */
    public function _initRoutes(Yaf_Dispatcher $dispatcher) {
        $router = $dispatcher->getRouter();
        //$router->addConfig(Yaf_Registry::get('config')->routes);
        Yaf_Loader::import(APP_CONFIG . '/route.php');
        $router->addConfig($routeConfigs);
    }
    /**
     * layout页面布局
     */
    public function _initLayout(Yaf_Dispatcher $dispatcher) {
        Yaf_Registry::set('dispatcher', $dispatcher);
    }
}
