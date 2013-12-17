<?php
class ErrorController extends Yaf_Controller_Abstract {

    private $_config;

    public function init(){
        $this->_config = Yaf_Application::app()->getConfig();
    }

    public function errorAction() {
        $exception = $this->getRequest()->getParam('exception');

        /*是否开启报错*/
        $showErrors = $this->_config->application->showErrors;
        /*报错以后记录日志*/
        $error_trace = $exception->getTraceAsString();
        $error_message = $exception->getMessage();
        //$this->writeLog(date('Y-m-d'),$error_trace.$error_message);        
        if($showErrors){
            $this->_view->trace = ($showErrors) ? $exception->getTraceAsString() : '';
            $this->_view->message = ($showErrors) ? $exception->getMessage() : '';            
        }else{     
            header('Content-Type: text/html;Charset=UTF-8');     
            echo "您请求的页面正繁忙，请稍后再试!";exit;
        }
    }

    private function _pageNotFound(){
        $this->getResponse()->setHeader('HTTP/1.0 404 Not Found');
        $this->_view->error = 'Page was not found';
    }

    private function _unknownError(){
        $this->getResponse()->setHeader('HTTP/1.0 500 Internal Server Error');
        $this->_view->error = 'Application Error';
    }
    
    public  function writeLog($filename, $message='') {
        $filename = PUB_PATH . "log/{$filename}.log";
        $datime = date("Y-m-d H:i:s")." ";
        file_put_contents($filename, $datime.$message."\n", FILE_APPEND);
    }    
}
