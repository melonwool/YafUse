<?php
class Db_Base
{
	public function __construct() {
		$this->_config = Yaf_Registry::get("config");
		$this->_db = new Db_Mysql ($this->_config->database->config->toArray());
		//$this->_redis = new Redis();
		//$this->_redis->connect($this->_config->redis->host);
	}
}