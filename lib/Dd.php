<?php
/**
* Web_Db.php
* used to handle operation for db connect,
* including db_connect, db_close and other functions
* by simonLau
* April 30, 2015
**/

 class Web_Db{
 	protected $_config = array();
 	protected $rdb = array();
 	protected $wdb = array();

 	public function __construct(array $config){
 		$this->_config = $config;
 	}

 	public function getConfig(){
 		return $this->_config;
 	}
 }