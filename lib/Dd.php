<?php
 class Db{
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