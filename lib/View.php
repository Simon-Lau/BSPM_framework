<?php

class Web_View extends Smarty{

	protected static $_instance = null;

	protected static $_rules = array();

	protected $_url = null;

	var $left_delimiter = '<!--{';
	var $right_delimiter = '}-->';

	function __construct(){
		$this->template_dir = TPL_DIR;
		$this->compile_dir = TMP_DIR.'/templates';
		$this->register_function('formhash', array('Web_View','formhash'));
		$this->register_function('show_pages', array('Web_View','show_pages'));
		$this->register_function('get_url', array('Web_View','get_url'));
		$this->assign_by_ref('param',Web_Request::$_params);
	}

	public static function get_url($params){
		
		$request = Web_Request::getInstance();

		$baseurl = isset($params['baseurl']) ? $params['baseurl'] : $request->getBaseUrl();

		if(empty($params)){
			return $baseurl;
		}

		$params['module'] = isset($params['module']) ? $params['module'] : $request->getModuleName();
		$params['controller'] = isset($params['controller']) ? $params['controller'] : $request->getControllerName();
		$params['action'] = isset($params['action']) ? $params['action'] : $request->getActionName();
		$exten = '.do';

		if(substr($params['module'], 0, 11) == 'javascript:'){
			$params['module'] = substr($params['module'], 11);
		}
		if(substr($params['controller'], 0, 11) == 'javascript:'){
			$params['controller'] = substr($params['controller'], 11);
		}
		if(substr($params['action'], 0, 11) == 'javascript:'){
			$params['action'] = substr($params['action'], 11);
		}
		
		if(isset($params['action'])){
			$url = ($params['module'] ? $params['module'].'/' : '' ).$params['controller'].'/'.$params['action'].$exten;
		}elseif(isset($params['controller'])){
			$url = ($params['module'] ? $params['module'].'/' : '' ).$params['controller'].$exten;
		}elseif(isset($params['module'])){
			$url = $params['module'].$exten;
		}else{
			$url = ($params['module'] ? $params['module'].'/' : '' ).$params['controller'].'/'.$params['action'].$exten;
		}

		$v = '?';
		foreach($params as $key => $value){
			if(in_array($key, array('module','controller','action'))){
				continue;
			}
		/*
			if(empty($value)){
				unset($params[$key]);
				continue;
			}
		*/
			if ( $key == 'ajax' )
			{
				$url .= $v."_r=".rand();
				continue;
			}
			if(substr($value, 0, 11) == 'javascript:'){
				$url .= $v.$key.'='.substr($value, 11);
			}else{
				$url .= $v.$key.'='.rawurlencode($value);
			}
			$v = '&';
		}

		return $baseurl.'/'.$url;
	}

	public static function getInstance(){
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	function addModuleDir($module){
		$tmpdir = (array) $this->template_dir;
		$tmd[] = TPL_DIR.'/'.$module;
		foreach($tmpdir as $d){
			$tmd[] = $d;
		}
		$this->template_dir = $tmd;
		$this->compile_dir .= '/'.$module;
	}

	function trigger_error($error_msg, $error_code = 500){
		throw new Web_Exception($error_msg, $error_code);
	}

	public static function formhash(){
		$formhash = substr(md5(time()+rand(0,1000)),0,8);
		@setcookie('formhash', $formhash, 0, '/');
		return $formhash;
	}

	public static function show_pages($params, &$smarty){
		$request = Web_Request::getInstance();

		if(($pagesize = $request->getParam('_pagesize')) < 2){
			return;
		}
		$total =$request->getParam('_pagetotal');
		$page = max(1,min($request->getParam('page'),$pagesize));

		$num = min(10, $pagesize);
		$offset = min(2, $num);
		$pagefrom = min($pagesize-$num+1, max($page-$offset,1));
		$pageto = min($pagefrom+$num-1, $pagesize);
		$params['page'] = '{page}';
		$page = array(
			'num'=>$num,
			'offset'=>$offset,
			'pagefrom'=>$pagefrom,
			'pageto'=>$pageto,
			'pagesize'=>$pagesize,
			'page'=>$page,
			'total'=>$total,
			'url' => self::get_url($params)
		);
		$smarty->assign('page',$page);
		return $smarty->fetch('page.tpl');
	}

}
?>