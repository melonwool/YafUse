<?php
class Db_Base
{
	public function __construct() {
		$this->_config = Yaf_Registry::get("config");
		$this->_db = new Db_Mysql ($this->_config->mysql->config->toArray());
	}
}
