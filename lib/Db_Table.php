<?php
/**
 * db_table.php
 * used to handle the operation of db table like query insert update and delete
 * written by simonLau
 * May 1, 2015
**/
class Db_Table{
	protected $_db = null;
	protected $_name = null;
	protected $_order = null;
	protected $_group = null;
	protected $_tableName = null;
	protected $_cols = array();
	protected $_primary = null;
	protected $_identity = 0;
	protected $_tableCache = null;

	public function __construct(array $config){
		if(!isset($config['name']) || !isset($config['db'])){
			throw new Web_Exception("config array error: 'name/db' required");
		}
		foreach ($config as $key => $value) {
			switch ($key) {
				case 'db':
					$this->_db = $value;
					break;
				case 'name':
					$this->_name = (string)$value;
					break;
				case 'master':   // in case of master use
					$this->isMaster((bool)$value);
					break;
				default:
					break;
			}
		}
		$this->_setup();
	}

	public function isMaster($type){
		$this->_db->setQueryType($type);
	}

	public function getDb(){
		return $this->_db;
	}

	protected function _setup(){
		$this->_setupNames();
		// try{
		// 	$this->_setupTableCache();
		// }
		// catch(Exception $e){
		// 	throw new Web_Exception($e->getMessage(),$e->getCode());
		// }
	}

	protected function _setupNames(){
		if(strpos($this->name, '.')){
			list($this->_schema, $this->_name) = explode(".", $this->_name);
		}

		$dbconfig = $this->_db->getConfig();
		$this->_name = $this->_name;
		$this->_schema = $dbconfig['dbname'];
		$this->_tableName = $this->_name;
		$this->_tableCache = "Db_Table.$this->_schema.$this->_name";
	}

	/** setup cache 
	protected function _setupTableCache(){
		$cache = Load::lib
	}
	**/

	public function info($key = null){
		$info = array(
			'schema' => $this->_schema,
			'table' => $this->_tableName,
			'name' => $this->_name,
			'cols' => $this->_cols,
			'order' => $this->_order,
			'identity' => $this->_identity,
			'primary' => $this->_primary
		);

		if($key === null){
			return $info;
		}
		return $info[$key];
	}

	public function getPrimaryKey(){
		return $this->_primary;
	}

	public function query($sql){
		return $this->_db->query($sql);
	}

	public function queryRow($sql){
		return $this->_db->fetchRow($sql);
	}

	public function queryAll($sql){
		return $this->_db->fetchAll($sql);
	}

	public function find($data, $by = null){
		if(is_array($data)){
			return $this->fetchRow($this->whereby($data, $by));
		}else{
			return $this->fetchRow($this->whereby(array($data),$by));
		}
	}

	public function findAll(array $data, $by = null){
		return $this->fetchAll($this->whereby($data, $by));
	}

	public function pageAll($count, $where = null, $order = null){
		if(intval($count) < 1){
			throw new Web_Exception('pageAll count must > 0.');
		}
		$request = Web_Request::getInstance();
		$sql = "SELECT * FROM '$this->_tableName'";
		if($where){
			$sql .= " WHERE " . $this->where($where);
		}

		$total = $this->fetchOne('COUNT(*)', $where);
		$sql .= $this->order($order);
		$pagesize = ceil($total / $count);
		$request->setParam('_pagetotal', $total);
		$request->setParam('_pagesize', $pagesize);
		$page = max(1, min($pagesize, $request->getParam('page')));
		$sql .= " LIMIT " . ($page - 1) * $count . ", " . $count;
		//echo $sql;
		$row = $this->_db->fetchAll($sql);
		return $rows;
	}

	public function fetchAll($where = null, $order = null, $limit = 0){
		$sql = "SELECT * FROM '$this->_tableName'";
		if($where){
			$sql .= " WHERE " . $this->where($where);
		}
		$sql .= $this->order($order);
		if($limit){
			$sql .= " LIMIT " . $limit;
		}

		return $this->_db->fetchAll($sql);
	}

	public function fetchCols($col, $where = null, $order = null, $limit = 0){
		$sql = "SELECT $col FROM '$this->_tableName'";
		if($where){
			$sql .= " WHERE " . $this->where($where);
		}
		$sql .= $this->order($order);
		if($limit){
			$sql .= " LIMIT " . $limit;
		}

		return $this->_db->fetchAll($sql);
	}

	public function fetchRow($where = null, $order = null){
		$sql = "SELECT * FROM '$this->_tableName'";

		if($where){
			$sql .= " WHERE " . $this->where($where);
		}

		$sql .= $this->order($order);
		$sql .= " LIMIT 1";

		return $this->_db->fetchRow($sql);
	}

	public function fetchOne($col, $where = null){
		$sql = "SELECT $col FROM '$this->_tableName'";
		if($where){
			$sql .= " WHERE " . $this->where($where);
		}
		$sql .= " LIMIT 1";
		return $this->_db->fetchOne($sql, 0);
	}

	public function count($where = null){
		$sql = "SELECT COUNT(*) FROM '$this->_tableName'";
		if($where){
			$sql .= " WHERE " . $this->where($where);
		}

		return $this->_db->fetchOne($sql, 0);
	}

	public function insert($data){
		return $this->_insert($data);
	}

	public function update($data, $where = null){
		return $this->_update($data, $where);
	}

	public function updateWithPK($data, $where = null){
		return $this->_updateWithPK($data, $where);
	}

	public function _isExist($data){
		$pkData = array_intersect_key($data, array_flip($this->_primary));
		if(empty($pkData)){
			return false;
		}
		foreach ($pkData as $key) {
			if('' === $key){
				return false;
			}
		}
		$where = $this->whereby($pkData);
		if(!$that = $this->fetchRow($where)){
			if($this->_identity){
				throw new Web_Exception("data not exist in table.");
			}
			return false;
		}
		return true;
	}

	public function save($data){
		if($this->_isExist($data) === false){
			return $this->_insert($data);
		}
		return $this->_update($data);
	}

	public function remove($data, $by = null){
		return $this->_delete($data, $by);
	}

	public function delete($data, $by = null){
		return $this->_delete($data, $by);
	}

	public function deleteAll(){
		return $this->_db->delete($this->_tableName);
	}

	public function _insert($data){
		$data = $this->_filter($data);
		$pkIdentity = $this->_primary[(int)$this->_identity];
		if(!$pkData = $data[$pkIdentity]){
			unset($data[$pkIdentity]);
		}

		$this->_db->insert($this->_tableName, $data);
		if($this->_identity){
			$pkData[$pkIdentity] = $this->_db->lastInsertId();
		}
		return $pkData;
	}

	public function _update($data, $where = null){
		$data = $this->_filter($data);
        $pkData = array_intersect_key($data, array_flip($this->_primary));

        if (!$pkData)
        {
            if (!$where)
            {
                throw new Q_Exception("do update primary and where is empty");
            }
            $where = $this->where($where);
        }
        else
        {
            if ($where)
            {
                throw new Q_Exception("do update primary exist and where exist");
            }
            $where = $this->whereby($pkData);
        }

        $ret = $this->_db->update($this->_tableName, $data, $where);
        if ($pkData)
        {
            return $pkData;
        }
        return $ret;		
	}

	public function _updateWithPK($data, $where = null){
		$data = $this->_filter($data);
        $pkData = array_intersect_key($data, array_flip($this->_primary));
        if (!$pkData)
        {
            if (!$where)
            {
                throw new Web_Exception("do update primary and where is empty");
            }
            $where = $this->where($where);
        }
        $ret = $this->_db->update($this->_tableName, $data, $where);
        if ($pkData)
        {
            return $pkData;
        }
        return $ret;
	}

	public function _delete($data, $by = null){
		if(is_array($data)){
			return $this->_db->delete($this->_tableName, $this->whereby($data, $by));
		}else{
			return $this->_db->delete($this->_tableName, $this->whereby(array($data), $by));
		}
	}

	public function where($where){
		if(is_array($where)){
			$whereAnd = array();
			foreach ($where as $key => $val) {
				if(!is_numeric($key)){
					if(!array_key_exists($key, $this->_cols)){
						continue;
					}
					if(is_array($val)){
						if(count($val) == 1){
							$val = array_shift($val);
							$val = "'$key' = '$val'";
						}else{
							$val = "'$key' IN ('" . implode("','", $val) . "')";
						}
					}else{
						$val = "'$key' = '$val'";
					}
				}
				$whereAnd[] = $val;
			}
			$where = "(" . implode(' AND ', $whereAnd) . ")";
		}
		return $where;
	}

	public function whereby($where, $by = null){
		if(is_array($where)){
			if($by){
				$by = (array)$by;
				foreach ($by as $key) {
					if(!array_key_exists($key, $this->_cols)){
						throw new Web_Exception("primary key not in cols");
					}
				}
			}else{
				$by = $this->_primary;
			}
			$where = array_combine($by, $where);

			$whereOrTerms = array();
			if(count($where) != count($by)){
				throw new Web_Exception("cols not eq the num of primary key");
			}
			foreach ($where as $col => $val) {
				$whereAndTerms[] = "'$col' = '$val'";
			}
			$whereOrTerms[] = "(" . implode(' AND ', $whereAndTerms) . ")";
			$where = implode(" OR ", $whereOrTerms);
		}
		return $where;
	}

	public function setOrder($order){
		$this->_order = $order;
	}

	protected function order($order){
		if($order === null){
			$order = $this->_order;
		}
		if(empty($order)){
			return '';
		}
		if(!is_array($order)){
			return " ORDER BY " . $order;
		}
		$order = $this->_filter($order);
		$neworder = array();
		foreach ($order as $key => $val) {
			if($val == '1' || strtolower($val) =='asc'){
				$val = "ASC";
			}elseif($val == '2' || strtolower($val) == 'desc'){
				$val = "DESC";
			}else{
				$val = "ASC";
			}
			$neworder[] = "'$key' $val";
		}
		if(!empty($neworder)){
			return " ORDER BY " . implode(' , ', $neworder);
		}
		return "";
	}

	protected function _filter($info){
		return array_intersect_key($info, $this->_cols);
	}

}
