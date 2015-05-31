<?php
class cache_file_Library
{
	protected $_cachedir = '__cache_file__';

	function __construct()
	{
		$this->_cachedir = TMP_DIR.'/'.$this->_cachedir;
	}

	function set($id, $data, $tags = array())
	{
		$data = "<?php\nreturn ".var_export($data,true)."\n;?>";
		$path = $this->_path($id, true);
		@file_put_contents($path, $data);
		@chmod($path,0777);
		$tags = (array) $tags;
		if(!empty($tags)){
			foreach($tags as $tag){
				$tid = '__tag__'.$tag;
				if(($data = $this->get($tid)) === false){
					$data = array();
				}
				$data[] = $id;
				$data = array_unique($data);
				$this->set($tid, $data);
			}
		}
	}

	function get($id, $expired=0)
	{
		$path = $this->_path($id, false);
		
		if ( $expired )
		{
			$ctime = @filectime($path);
		
			if ( $ctime &&  ( $ctime + $expired ) < time() )
			{
				return false;
			}	
		}

		$data = @include($path);
		return $data;
	}

	function remove($id = null)
	{
		if(is_null($id)){
			@exec('rm -rf '.$this->_cachedir.'/*');
		}else{
			$path = $this->_path($id, false);
			@unlink($path);
		}
	}

	function removeTag($tag)
	{
		$tid = '__tag__'.$tag;
		if(($ids = $this->get($tid)) === false){
			return;
		}
		foreach($ids as $id){
			$id && $this->remove($id);
		}
		$this->remove($tid);
	}

	protected function _path($id, $mkdirs = true)
	{
		$filename = md5($id).'.php';
		$root_dir = $this->_cachedir.DIRECTORY_SEPARATOR;
		if ($mkdirs && !is_dir($root_dir)){
			$umask = @umask(0);
			@mkdir($root_dir, 0777, true);
			@umask($umask);
		}
		return $root_dir . $filename;
	}
}