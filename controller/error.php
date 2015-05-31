<?php
class error_Controller extends Controller{
	
	function errorAction(){
		$exception = $this->_get('_exceprion');
		switch($exception->getCode()){
		case 9001://没有权限
			$this->noacl($exception);
			break;
		case 404:
			$this->nopage($exception);
			break;
		case 500:
			$this->exception($exception);
			break;
		default:
			$this->exception($exception);
			break;
		}
		exit;
	}

	function noacl($error = null){
		$this->showmsg('没有权限',1);
		exit;
	}

	function exception($error = null){
		if(DEBUG_MODE > 0){
			var_dump($error);exit;
		}
		$this->_response->setHttpResponseCode(500);
		$this->showmsg('服务器繁忙，请您稍后重试!',1);
		exit;
	}

	function nopage($error = null){
		if(DEBUG_MODE > 1){
			var_dump($error);exit;
		}
		$this->_response->setHttpResponseCode(404);
		$this->assign('msg', '您访问的页面不存在！');
		$this->assign('type', 1);
		$this->display('message.tpl');
		exit;
	}
}
?>