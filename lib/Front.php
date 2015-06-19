<?php
/**
* Front.php
* used to init, first request
* by simonLau
* May 12, 2015
**/
class Web_Front{
	protected static $_instance = null;
	protected $_request = null;
	protected $_response = null;
	protected $_modules = array();
	protected $_plugins = array();

	protected function __construct(){
		$this->_request = Web_Request::getInstance();
		$this->_response = Web_Response::getInstance();
	}

	public static function getInstance(){
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function getModule($key){
		return isset($this->_modules[$key])? CTRL_DIR.DIRECTORY_SEPARATOR.$key : CTRL_DIR;
	}

	public function addPlugin($plugin){
		if(false === array_search($plugin, $this->_plugins, true)){
			$this->_plugins[] = $plugin;
		}
		return $this;
	}

	public function removePlugin($plugin){
		if(is_string($plugin)){
			foreach ($this->_plugins as $key => $value) {
                            $type = get_class($value);
                                if($plugin == $type){
                                        unset($this->_plugins[$key]);
                                }
			}
		}else{
			$key = array_search($plugin, $this->_plugins, true);
			if(false !== $key){
				unset($this->_plugins[$key]);
			}
		}
		return $this;
	}

	public function execPlugin($method){
		foreach ($this->_plugins as $key => $value) {
			try{
				$plugin->$method();
			}catch(Exception $e){
				$this->error($e);
			}
		}
	}

	public function route(){
		$path = $this->_request->getPathInfo();
		$param = array();
		$path = trim($path, '/');
		$_routed = false;
		$cache = Load::lib('cache_file');

		$cacheKey = "Web_Front.route.modules".APP_NAME;
		if(($this->_modules = $cache->get($cacheKey)) === false){
			$dir = new DirectoryIterator(CTRL_DIR);
			foreach ($dir as $file) {
				if($file->isDir() && !$file->isDot()){
					$_moduledir = $file->getFilename();
					$this->_modules[$_moduledir] = $_moduledir;
				}
			}
			$cache->set($cacheKey, $this->_modules);
		}
		if($path != ''){
			if($pos = strrpos($path, '.')){
				$path = substr($path, 0, $pos);
			}
			$path = explode('/', $path);
			if(count($path) > 3){
				throw new Web_Exception("path depth validation failure", 404);
			}
			$setup = $params['controller'] = $path[0];
			$params['action'] = $path[1];
			if(!empty($this->_modules[$setup])){
				$params['module'] = $setup;
				$params['controller'] = $path[1];
				$params['action'] = $path[2];
			}
		}
		foreach($params as $param => $value){
			if($param === 'module'){
				$this->_request->setModuleName($value);
			}elseif($param === 'controller'){
				$this->_request->setControllerName($value);
			}elseif($param === 'action'){
				$this->_request->setActionName($value);
			}else{
				$this->_request->setParam($param, $value);
			}
		}
	}

	public function run(){
		$this->execPlugin('pre_route');
		try{
			$this->route();
		}catch(Exception $e){
			$this->error($e);
		}
		$this->dispatch(); 
	}

	public function dispatch(){
		ob_start();
		try{
			$this->execPlugin('pre_dispatch');
			$this->_dispatch();
		}catch(Exception $e){
//			$this->error($e);
		}
//		$this->_response->send();
	}

	protected function _dispatch(){
		$loadController = false;
		$moduleName = $this->_request->getModuleName();
		$controllerName = $this->_request->getControllerName();
		$loadFile = $this->getModule($moduleName).'/'.$controllerName.'.php';
		if(include_once($loadFile)){
			$className = $controllerName .'_Controller';
		}else{
			throw new Web_Exception("controller $controllerName not found", 404);
		}
		$action = $this->_request->getActionName();
		$action = $action . 'Action';
		$controller = new $className();
		$controller->$action();
	}

	public function error($e){
		ob_get_clean();
		$this->_request->setParam('_exception', $e)->setModuleName(null)->setControllerName('error')->setActionName('error');
		try{
                    $this->_dispatch();
		}catch(Exception $e){
	            var_dump($e);
		}
		exit;
	}
}