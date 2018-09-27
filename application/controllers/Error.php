<?php
/**
 * 一般错误搜集-一般是框架内部错误才会走这里的流程
 * 这里未能捕获的错误,会直接走errorHandler
 */
class ErrorController extends Yaf_Controller_Abstract {
    public function errorAction(Exception $exception) {
        (new Raven())->LogException($exception);
        Yaf_Dispatcher::getInstance()->autoRender(false);
        $e = $exception->getPrevious();
        if (!empty($e)){
            $errors = $e;
            $errMessage = $e->getMessage();
            $errFile = $e->getFile();
            $errLine = $e->getLine();
        }else{
            $errors = $exception;
            $errMessage = $exception->getMessage();
            $errFile = $exception->getFile();
            $errLine = $exception->getLine();
        }
        switch ($exception->getCode()) {
        case YAF_ERR_AUTOLOAD_FAILED:
        case YAF_ERR_NOTFOUND_MODULE:
        case YAF_ERR_NOTFOUND_CONTROLLER:
        case YAF_ERR_NOTFOUND_ACTION:
        case YAF_ERR_NOTFOUND_VIEW:
            header('HTTP/1.1 404 Not Found');
            Log::error(Log::NOTICE, $errMessage . ' IN FILE ' . $errFile . ' ON LINE ' . $errLine);
            break;
        default:
            //记录文件错误日志
            Log::error(Log::ERROR, $errMessage . ' IN FILE ' . $errFile . ' ON LINE ' . $errLine);
            header('HTTP/1.1 500 Internal Server Error');
            break;
        }
        //这里添加了使用sentry进行异常捕获
        //sentry错误搜集
    }
}
