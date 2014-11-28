<?php
/**
 * yaf 框架报错类调用
 *  默认错误会调用这个Controller 中 ErrorAction
 */
class ErrorController extends Yaf_Controller_Abstract {
    private $_config;
    public function init(){
        $this->_config = Yaf_Application::app()->getConfig();
    }
    /**
     * [具体错误处理]
     * @param  Exception $exception [description]
     * @return [type]               [description]
     */
    public function errorAction(Exception $exception)
    {
        Yaf_Dispatcher::getInstance()->autoRender(false);
        if ($this->_config->application->showErrors) {
            switch ($exception->getCode()) {
                case YAF_ERR_AUTOLOAD_FAILED:
                case YAF_ERR_NOTFOUND_MODULE:
                case YAF_ERR_NOTFOUND_CONTROLLER:
                case YAF_ERR_NOTFOUND_ACTION:
                case YAF_ERR_NOTFOUND_VIEW:
                    if (strpos($this->getRequest()->getRequestUri(), '.css') !== false ||
                        strpos($this->getRequest()->getRequestUri(), '.jpg') !== false ||
                        strpos($this->getRequest()->getRequestUri(), '.js') !== false ||
                        strpos($this->getRequest()->getRequestUri(), '.png') !== false ||
                        strpos($this->getRequest()->getRequestUri(), '.ico') !== false ||
                        strpos($this->getRequest()->getRequestUri(), '.gif') !== false
                    ) {
                        header('HTTP/1.1 404 Not Found');
                    }
                default:
                    //记录错误日志
                    Log::error('error',$exception->getMessage() . ' IN FILE ' . $exception->getFile() . ' ON LINE ' . $exception->getLine()); 
                    //显示错误信息
                    $this->_view->exception =  $exception;
                    echo $this->getView()->render(APP_PATH.'/views/error/error.html');
            }
        } else {
            //禁止输出视图内容
            Yaf_Dispatcher::getInstance()->enableView();
            switch ($exception->getCode()) {
                case YAF_ERR_AUTOLOAD_FAILED:
                case YAF_ERR_NOTFOUND_MODULE:
                case YAF_ERR_NOTFOUND_CONTROLLER:
                case YAF_ERR_NOTFOUND_ACTION:
                case YAF_ERR_NOTFOUND_VIEW:
                    header('HTTP/1.1 404 Not Found');
                    //记录日志
                    Log::error('error',$exception->getMessage() . ' IN FILE ' . $exception->getFile());
                    $this->_view->type='err404';
                    $this->_view->display(APP_PATH.'/views/404.php');
                    break;
                default:
                    header('HTTP/1.1 500 Internal Server Error');
                    //记录文件错误日志
                    Log::error('error',$exception->getMessage() . ' IN FILE ' . $exception->getFile() . ' ON LINE ' . $exception->getLine());                    
                    //记录sentry错误日志
                    $this->_view->type='error';
                    $this->_view->display(APP_PATH.'/views/404.php');
                    break;
            }
        }
    }
}
