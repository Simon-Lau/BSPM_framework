<?php
Class cache_Library
{
	protected $_cache = array();
	protected $_prefix = '';
	protected $_expires = null;

	public function __construct(){
		if(!$config = @Load::conf('cache')){
			throw new Q_Exception("Cache config file not found");
		}
		foreach($config as $backend => $value){
			switch($backend){
				case 'file':
				case 'ea':
				case 'mem':
					$this->_cache[] = Load::lib('cache_'.$backend, $value);
				break;
				case 'expires':
					$this->_expires = $value;
				break;
				case 'prefix':
					$this->_prefix = $value;
				break;
				default:
					throw new Q_Exception("Cache($backend) not support");
				break;
			}
		}
		
	}

	public function get($key){
		if (DEBUG_MODE > 1) { return; }
		$key = $this->_prefix.$key;
		foreach($this->_cache as $cache){
			if($res = $cache->get($key)){
				return $res;
			}
		}
	}

	public function set($key, $value = null, $expires = null){
		if (DEBUG_MODE > 1) { return false; }
		$expires = $expires ? $expires : $this->_expires;
		$key = $this->_prefix.$key;
		foreach($this->_cache as $cache){
			$res = $cache->set($key, $value, $expires);
		}
		return $res;
	}

	public function remove($key = null)
	{
		$key = $this->_prefix.$key;
		foreach($this->_cache as $cache){
			$res = $cache->remove($key);
		}
		return $res;
	}
}
