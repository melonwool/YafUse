<?php
define('APP_ROOT', dirname(__DIR__));
define("APP_PATH",  dirname(__DIR__).'/application');
define("APP_CONFIG", APP_PATH.'/conf');
//定义全局library
ini_set('yaf.library', APP_PATH.'/library');
//第二个参数用来区分开发环境、测试环境、生产环境配置 对应config中内容
$app  = new Yaf_Application( APP_CONFIG."/app.ini",'develop');
$app->bootstrap()->run();
