<?php
/**
 * Model.php
 * written by simonLau
 * create on April 30, 2015
 * modify latest May 2, 2015
**/
abstract class Model{
	protected $_error = array();
	protected $_table = null;
	protected $_check = array();
	protected $_cachekey = null;
	protected $_cache = null;

	public function __construct(){
		$this->_cacheTag = $this->_cacheKey = get_class($this);
		$this->init();
	}

	public function init(){}

	public function __call($method, $args = array()){
		if($this->_table === null){
			throw new Web_Exception('Model _table does not exist', 500);
		}
		return call_user_func_array(array($this->_table, $method),$args);
	}

	public function getError(){
		if(!$this->isError()){
			$this->_error[] = "server is busy and try it later";
		}
		return $this->_error;
	}

	protected function setError($error){
		if(is_array($error)){
			$this->_error = $error;
		}elseif(is_string($error)){
			$this->_error[] = $error;
		}
		return $this;
	}

	protected function isError(){
		return !empty($this->_error);
	}

	protected function checkPost($info, $check = 1){
		if($this->_check && $check > 0){
			$validator = Load::lib('validator');
			if($error = $validator->check($info, $this->_check)){
				$this->setError($error);
				return false;
			}
		}
		if($check >1){
			if(!$_POST['formhash'] || $_POST['formhash'] != $_COOKIE['formhash']){
				$ths->setError('from is out of time and submit it again');
				return false;
			}
			setcookie('formhash', '', -86400, '/');
		}
		return true;
	}

	public function remove($id){
		$this->clearCache();
		return $this->_table->remove($id);
	}

	public function save($info, $check = 1){
		if($this->_table === null){
			throw new Web_Exception("Model $this->_table does not exist", 500);
		}

		$this->checkPost($info, $check);
		if($this->isError()){
			return false;
		}
		$this->clearCache();
		return $this->_table->save($info);
	}

	public function clearCache(){
		if($this->_cache){
			$this->_cache->removeTag($this->_cacheTag);
		}
	}

	public function setCache($data, $key = null){
		if($this->_cache){
			$key = $key ? $this->_cachekey.$key : $this->_cachekey;
			return $this->_cache->set($key, $data, $this->_cacheTag);
		}
	}

	public function getCache($key = null){
		if($this->_cache){
			$key = $key ? $this->_cachekey.$key : $this->_cachekey;
			return $this->_cache->get($key);
		}
	}
}