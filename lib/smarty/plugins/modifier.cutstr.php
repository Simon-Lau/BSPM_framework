<?php

function smarty_modifier_cutstr($string, $length = 80, $etc = '...')
{
	if ($length == 0)
        return '';
	$string = strip_tags($string);
	$string = str_replace(array("\r\n","&nbsp;"),array(" "," "),$string);
	if (strlen($string) > $length) {
		$length -= strlen($etc);
		for($i=0; $i < $length; $i++){
			$tmpstr=(ord($string[$i])>=161 && ord($string[$i])<=254&& ord($string[$i+1])>=161 && ord($string[$i+1])<=254)?$string[$i].$string[++$i]:$tmpstr=$string[$i];
			if($i<$length)$tmp .= $tmpstr;
		}
		$string = $tmp.$etc;
	}
	return $string;
}

?>
