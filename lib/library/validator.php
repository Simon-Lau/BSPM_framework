<?php
/**
 * validator.php
 * written by simonLau
 * created on May 2, 2015
**/
class validator_Library{
	protected $_check = array(
		'require' => '.+',
		'email' => '^\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*$',
		'phone' => '^((\\(\\d{2,3}\\))|(\\d{3}\\-))?(\\(0\\d{2,3}\\)|0\\d{2,3}-)?[1-9]\\d{6,7}(\\-\\d{1,4})?$',
		'mobile' => '^((\\(\\d{2,3}\\))|(\\d{3}\\-))?13\\d{9}$|15\\d{9}|18\\d{9}',
		'url' => '^http:\\/\\/[A-Za-z0-9]+\\.[A-Za-z0-9]+[\\/=\\?%\\-&_~`@[\\]\\\':+!]*([^<>\\"\\"])*$',
		'number' => '^\\d+$',
		'zip' => '^\\d{6}$',
		'qq' => '^[1-9]\\d{4,11}$',
		'int' => '^[-\\+]?\\d+$',
		'double' => '^[-\\+]?\d+(\\.\\d+)?$',
		'w' => '^\\w+$',
		'idcard' => 'is_idcard',
		'limit' => 'is_limit',
		'date' => 'is_date',
		'range' => 'is_range'
	);

	public function check($info, $check){
		$_error = array();
		foreach ($check as $key => $value) {
			$_tmp = (array)$value[0];
			$errormsg = empty($value[1]) ? "$key not found" : $value[1];
			foreach ($_tmp as $k => $v) {
				if(empty($val) && empty($info[$key])){
					unset($_error[$key]);
					break;
				}elseif(!is_int($k) && is_array($v) && $method = $this->_check){
					array_unshift($v, $info[$key]);
					if(call_user_func_array(array($this, $method), $v) !== true){
						$_error[$key] = $errormsg;
						break;
					}
				}else{
					if(!$preg = $this->_check[$v]){
						$preg = $v;
					}
					
					if($preg && !preg_match("#$preg#", $info[$key])){
						
						$_error[$key] = $errormsg;
						break;
					}
				}
			}
		}
		return $_error;
	}

	protected function is_limit($value, $min = null, $max = null){
		$len = strlen($value);
		if($min !== null && $len < $min){
			return false;
		}
		if($max !== null && $len > $max){
			return false;
		}
		return true;
	}

	protected function is_date($value, $format = null){
		if(($time = strtotime($value)) === false){
			return false;
		}
		if($format && date($format, $time) != $value){
			return false;
		}
		return true;
	}

	protected function is_range($value, $min =null, $max = null){
		if($min !== null && $value < $min){
			return false;
		}
		if($max !== null && $value > $max){
			return false;
		}
		return true;
	}
}