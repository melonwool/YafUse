<?php
define("DS", '/');
define("APP_PATH",  dirname(__FILE__).DS.'application'.DS);
define("PUB_PATH",dirname(__FILE__).DS.'public'.DS);
session_start(); 
require_once APP_PATH .'Functions.php';
$app  = new Yaf_Application(APP_PATH . "conf/application.ini");
$app->bootstrap()->run();