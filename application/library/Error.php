<?php

class Error extends Yaf_Controller_Abstract
{
    /**
     * [错误处理函数]
     * @param  [type] $errno   [description]
     * @param  [type] $errstr  [description]
     * @param  [type] $errfile [description]
     * @param  [type] $errline [description]
     * @return [type]          [description]
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {

        if (error_reporting() & $errno) {
            throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
        } else {
            switch ($errno) {
                case E_NOTICE:
                case E_USER_NOTICE:
                    //self::getLogHandler()->notice($errstr . ' IN FILE ' . $errfile . ' ON LINE ' . $errline);
                   break;

                case E_WARNING:
                case E_CORE_WARNING:
                case E_COMPILE_WARNING:
                case E_USER_WARNING:
                case E_DEPRECATED:
                case E_USER_DEPRECATED:
                    //self::getLogHandler()->warn($errstr . ' IN FILE ' . $errfile . ' ON LINE ' . $errline);
                    break;

                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                case E_STRICT:
                case E_RECOVERABLE_ERROR:
                case E_ALL:
                default:
                    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
                    break;
            }
        }
        return true;
    }

}