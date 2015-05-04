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

	public function array_trim(&$arr){
		foreach ($arr as $key => $value) {
			if(is_array($value)){
				$this->array_trim($value);
			}else{
				$arr[$key] = trim($value);
			}
		}
	}

	public function escape($string = null, $type = 'html'){
		if($string === null){
			self:: $_params = $this->escape(self::$_params);
			return self::$_params;
		}
		if(is_array($string)){
			foreach ($string as $key => $val) {
				$string[$key] = $this->escape($val, $type);
			}
			return $string;
		}else{
			switch($type){
				case 'html':
				return htmlspecialchars($string, ENT_QUOTES);
				case 'trim':
				return trim($string);
				default:
				return $string;
			}
		}
	}

	public static function getInstance(){
		if(null === self::$_instance){
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function getModuleName(){
		return $this->_module;
	}

	public function setModuleName($value){
		$this->_module = strtolower($vaule);
		return $this;
	}

	public function getControllerName(){
		return $this->_controller;
	}

	public function setControllerName($value){
		$value && $this->_controller = strtolower($value);
		return $this;
	}

	public function getActionName(){
		return $this->_action();
	}

	public function setActionName($value){
		$value && $this->_action = strtolower($value);
		return $this;
	}

	public function getRequestUri(){
		return $this->_requestUri;
	}

	public function setRequestUri(){
		$this->_requestUri = $_SERVER['REQUEST_URI'];
		return $this;
	}

	public function setParam($key, $value){
		$key = (string)$key;
		if((null === $value) && isset(self::$_params[$key])){
			unset(self::$_params[$key]);
		}elseif(null !== $value){
			self::$_params[$key] = $value;
		}

		return $this;
	}

	public function getParam($key, $default = null){
		if(isset(self::$_params[$key])){
			return self::$_params[$key];
		}elseif($default === null){
			return null;
		}
		return $default;
	}

	public function getParams(){
		return self::$_params;
	}

	public function isAjaxRequest(){
		if($this->getParam('inajax')){
			return true;
		}
		return (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}

	public function setBaseUrl($baseUrl = null){
		if($baseUrl === null){
			if(isset($_SERVER['SCRIPT_NAME'])){
				$baseUrl = $_SERVER['SCRIPT_NAME'];
			}elseif(isset($_SERVER['PHP_SELF'])){
				$baseUrl = $_SERVER['PHP_SELF'];
			}else{
				$this->_baseUrl = '';
				return $this;
			}
			$this->_baseUrl = rtrim(dirname($baseUrl), '/');
			return $this;
		}
		$this->_baseUrl = rtrim($baseUrl, '/');
		return $this;
	}

	public function getBaseUrl(){
		if(null === $this->_baseUrl){
			$this->setBaseUrl();
		}
		return $this->_baseUrl;
	}

	public function setPathInfo($pathInfo = null){
		if($pathInfo === null){
			$baseUrl = $this->getBaseUrl()
			$requestUri = $this->getRequestUri();
			if($pos = strpos($requestUri, '?')){
				$requestUri = substr($requestUri, 0, $pos);
			}
			$pathInfo = substr($requestUri, strlen($baseUrl));
		}
		$this->_pathInfo = (string)$pathInfo;
		return $this;
	}

	public function getPathInfo(){
		return $this->_pathInfo;
	}
}