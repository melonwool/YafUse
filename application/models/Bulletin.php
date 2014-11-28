<?php
class BulletinModel extends Db_Base
{
	/**
	 * 插入数据
	 * @param [str] $table [插入数据的数据表名]
	 * @param [array] $data  [数据数组，key-value]
	 * return id 插入数据的id
	 */
	public function InsertList($table, $data){
		$lastid = $this->_db->insert($table, $data);
		return $lastid;
	}
	/**
	 *  插入发送的公告
	 * @param [str] $table   [数据表名称]
	 * @param [array] $columns [数据字段名]
	 * @param [array(array())] $rows    [2维数组的 数据内容]
	 */
	public function InsertQueue($table, $columns, $rows){
		return $this->_db->insertMultiple($table, $columns, $rows);
	}
	/**
	 * 删除数据
	 * @param [type] $table [数据表名]
	 * @param [array] $condition    [条件]
	 */
	public function Delete($table, $conditions){
		return $this->_db->delete($table, $conditions);
	}
	/**
	 * 获取数据列表
	 */
	public function GetList(){
		$time = time();
		$sql = "SELECT * FROM `BulletinList` WHERE endtime > '{$time}'";
		return $this->_db->query($sql);		
	}
	/**
	 * 获取固定时间段内数据获取(默认5分钟)
	 * @param [type] $startime [开始时间]
	 */
	public function GetQueueList($startime){
		$endtime = $startime + 300;
		$sql = "SELECT content FROM `BulletinQueue` WHERE `logtime`>'{$startime}' AND `logtime`<='{$endtime}' ";
		try{
			return $this->_db->query($sql);
		} catch( Exception $e){
			return array();
		}
	}
	/**
	 * 将数据添加到redis队列
	 * @param [str] $data [str数据]
	 */
	public function PushData($data){
		$this->_redis->lPush('gmbulletin', $data);
		return true;
	}
	/**
	 * [GetQueue 获取队列中数据]
	 */
	public function GetQueue(){
		return $this->_redis->rPop('gmbulletin');
	}
}