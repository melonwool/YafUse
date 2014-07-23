<?php
define("DS", '/');
define("APP_PATH",  dirname(__FILE__).DS.'application'.DS);
define("PUB_PATH",dirname(__FILE__).DS.'public'.DS);
session_start(); 
require_once APP_PATH .'Functions.php';
//第二个参数用来区分开发环境、测试环境、生产环境配置 对应config中内容
$app  = new Yaf_Application(APP_PATH . "conf/application.ini",'develop');
$app->bootstrap()->run();
