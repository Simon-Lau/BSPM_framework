<?php

class cache_mem_Library{
	private $_mem = null;
	private $_expires = 86400;
	function __construct(array $config){
		if(!empty($config['expires'])){
			$this->_expires = $config['expires'];
		}
		$this->_mem = new Memcached;
		$this->_mem->addServers($config['servers']);
	}

	function get($key){
		return $this->_mem->get($key);
	}

	function set($key, $value = null, $expires = null){
		$expires = $expires ?  $expires: $this->_expires;
		return $this->_mem->set($key, $value, $expires);
	}

	function remove($key = null){
		if($key === null){
			return $this->_mem->flush();
		}else{
			return $this->_mem->delete($key);
		}
	}
}

?>
