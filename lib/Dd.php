<?php
/**
* Web_Db.php
* used to handle operation for db connect,
* including db_connect, db_close and other functions
* by simonLau
* April 30, 2015
**/

class Web_db{

 	protected $_config = array();
 	protected $_rdb = null;   //read db
 	protected $_wdb = null;   //write db
 	protected $_queryType = false;  // false is for read query and true for write query

 	public function __construct(array $config){
 		if(!isset($config['dbname']) || !isset($config['write'])){
 			throw new Web_Exception("config array error : dbname/write required");
 		}
 		$this->_config = $config;
 	}

 	public function getConfig(){
 		return $this->_config;
 	}

    public function closeDb(){
    	$this->_rdb = $this->_wdb = null;
    }

    public function setQueryType($type){
		$this->_queryType = $type;
	}

	public function query(){
		return $this->_query($sql);
	}

	public function exec($sql){
		return $this->_exec($sql);
	}

	public function fetchAll($sql){
		$stmt = $this->query($sql);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function fetchRow($sql){
		$stmt = $this->query($sql);
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function fetchOne($sql, $i = 0){
		$stmt = $this->query($sql);
		return $stmt->fetchColumn($i);
	}

	public function insert($table, array $data){
		$cols = array();
		$vals = array();
		foreach($data as $col => $val){
			$cols[] = "'$col'";
			$vals[] = "'$val'";
		}
		$sql = "INSERT INTO ".$table." (".implode(',', $cols) . ") " . "VAULES (" . implode(',', $vals) . ")";
		return $this->exec($sql);
	}

	public function update($table, array $data, $where = ''){
		$set = array();
		foreach ($data as $col => $val) {
			$set[] = "'$col' = '" . $val ."'";
		}
		$sql = "UPDATE " . $table . " SET " .implode(',', $set).(($where) ? " WHERE $where" : "");
		return $this->exec($sql);
	}

	public function delete($table, $where = ''){
		$sql = "DELETE FROM " . $table . (($where)? " WHERE $where" : "");
		return $this->exec($sql);
	}

	public function lastInsertId(){
		$this->_wconnect();
		return $this->_wdb->lastInsertId();
	}

	public function listTables(){
		$stmt = $this->query("SHOW TABLES");
		return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
	}

 	protected function _connect($config){
 		if(!isset($config['dbhost']) || !isset($config['username']) || !isset($config['password'])){
 			throw new Web_Exception("config array error dbhost/username/password required")
 		}
 		// still some problem with zkname
 		if(strpos($config['dbhost'],'www.') > 0){
 		// 	require_once('/zk_agent/names/nameapi.php');
			// $zk_host = new ZkHost;

			// $ret = getHostByKey($config['dbhost'], $zk_host);
			// if(0 == $ret && !empty($zk_host->ip) && !empty($zk_host->port))
			// {
			// 	$config['dbhost'] = $zk_host->ip;
			// 	$config['port'] = $zk_host->port;
			// }  
		}

		$dsn = 'mysql:'.'host='.$config['dbhost'].';dbname='.$this->_config['dbname'];
		if(isset($config['port'])){
			$dsn .= ';port=' . $config['port'];
		}
		$driver_options = array();
		if(isset($this->_config['charset'])){
			$driver_options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET character_set_connection=".$this->_config['charset'].", character_set_results=".$this->_config['charset'].", character_set_client=binary,sql_mode=''";
		}
		try{
			$db = new PDO($dsn,$config['username'],$config['password'],$driver_options);
		}catch(PDOException $e){
			throw new Web_Exception($e->getMessage(),$e->getCode());
		}
		return $db;
 	}

 	protected function _rconnect(){
 		if($this->_rdb){
 			return $this->_rdb;
 		}
 		if(!isset($this->_config['read'])){
 			$this->_rdb = $this->_wconnect();
 		}else{
 			$this->_rdb = $this->_connect($this->_config['read']);
 		}
 		return $this->_rdb;
 	}

 	protected function _wconnect(){
 		if($this->_wdb){
 			return $this->_wdb;
 		}
 		$this->_wdb = $this->_connect($this->_config['write']);
 		return $this->_wdb;
 	}

 	protected function _query($sql){
 		if($this->_queryType){
	 		$this->_wconnect();
	 		if(!$stmt = $this->_wdb->query($sql)){
	 			$tmparr = $this->_wdb->errorInfo();
	 			throw new Web_Exception($tmparr[2].'), SQL:'.$sql, $tmparr[1]);
	 		}
 		}else{
 			$this->_rconnect();
	 		if(!$stmt = $this->_rdb->query($sql)){
	 			$tmparr = $this->_rdb->errorInfo();
	 			throw new Web_Exception($tmparr[2].'), SQL:'.$sql, $tmparr[1]);
	 		}
 		}
 		return $stmt;
 	}

 	protected function _exec($sql){
 		$this->_wconnect();
 		if($rowCount = $this->_wdb->exec($sql) == false){
			$tmparr = $this->_wdb->errorInfo();
	 		throw new Web_Exception($tmparr[2].', SQL:'.$sql, $tmparr[1]);
 		}

 		return $rowCount;
 	} 	
 	
}