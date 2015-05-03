<?php
/**
 * Request.php
 * written by simonLau
 * create on May 3, 2015
**/
class Web_Request{
	protected static $_instance = null;
	protected $_dispatched = false;
	protected $_module = null;
	protected $_controller = 'index';
	protected $_action = 'index';
	protected $_requestUri;
	protected $_baseUrl = null;
	protected $_pathInfo = null;
	public stati $_params = array();

	public function __construct(){
		$this->setRequestUri();
		$this->setPathInfo();
		$this->addslashes($_COOKIE);
		self::$_params = $this->addslashes($_POST) + $this->addslashes($_GET);
		$this->array_trim(self::$_params);
	}

	public function addslashes(&$string, $force = false){
		if(get_magic_quotes_gpc() && $force === false){
			return $string;
		}
		if(is_array($string)){
			foreach ($string as $key => $val) {
				$string[$key] = $this->addslashes($val);
			}
		}else{
			$string = addslashes($string);
		}
		return $string;
	}
}