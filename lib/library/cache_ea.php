<?php

class cache_ea_Library{
	private $_mem = null;
	private $_lifetime = 86400;
	function get($key){
		return NULL;  //eaccelerator_get($key);
	}

	function set($key, $value = null, $expires = null){
	    return true;
	    /*
		$expires = is_null($expires) ?  $this->_lifetime : $expires;
		return eaccelerator_put($key, $value, $expires);
		*/
	}

	function remove($key = null){
	    return true;
	    /*
		if($key === null){
			return eaccelerator_gc();
		}else{
			return eaccelerator_rm($key);
		}
		*/
	}
}

?>
