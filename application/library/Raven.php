<?php
require_once dirname(__FILE__) . '/Raven/Autoloader.php';
Raven_Autoloader::register();

class Raven
{
    public $url;
    public $sentryClient;

    public function __construct()
    {
        $config = Yaf_Application::app()->getConfig()->toArray();
        $this->url = $config["sentry"]["dsn"];
        $this->sentryClient = new Raven_Client($this->url);
    }

    public function LogException($exception)
    {
        $this->sentryClient->captureException($exception);
    }
}
