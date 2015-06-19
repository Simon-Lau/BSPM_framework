<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// Define a destination
$targetFolder = '/finder/uploads'; // Relative to the root
if (!empty($_FILES)) {
        $fileName = $_POST['user_name'];
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
        $last = explode(".", $_FILES['Filedata']['name'])[1];
	$targetFile = rtrim($targetPath,'/') . '/user_tag_' . $fileName.".".$last;
	// Validate the file type
	$fileTypes = array('jpg','JPG','jpeg','gif','png','PNG','gif'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	if (in_array($fileParts['extension'],$fileTypes)) {
            resizeImage($tempFile,400,300,rtrim($targetPath,'/') . '/user_tag_' . $fileName,'.'.$last);
//		move_uploaded_file($tempFile,$targetFile);
		echo '1';
	} else {
		echo 'Invalid file type.';
	}
}

/**
	 +------------------------------------------------------------------------------
	 *                等比例压缩图片
	 +------------------------------------------------------------------------------
	 * @param  String $src_imagename 源文件名        比如 “source.jpg”
	 * @param  int    $maxwidth      压缩后最大宽度
	 * @param  int    $maxheight     压缩后最大高度
	 * @param  String $savename      保存的文件名    “d:save”
	 * @param  String $filetype      保存文件的格式 比如 ”.jpg“
	 * @version  1.0
	 +------------------------------------------------------------------------------
	 */
function resizeImage($src_imagename,$maxwidth,$maxheight,$savename,$filetype)
{
    $im=imagecreatefromjpeg($src_imagename);
    $current_width = imagesx($im);
    $current_height = imagesy($im);
    if(($maxwidth && $current_width > $maxwidth) || ($maxheight && $current_height > $maxheight))
    {
        if($maxwidth && $current_width>$maxwidth)
        {
            $widthratio = $maxwidth/$current_width;
            $resizewidth_tag = true;
        }

        if($maxheight && $current_height>$maxheight)
        {
            $heightratio = $maxheight/$current_height;
            $resizeheight_tag = true;
        }

        if($resizewidth_tag && $resizeheight_tag){
            if($widthratio < $heightratio){
                $ratio = $widthratio;
            }
            else{
                $ratio = $heightratio;
            }
        }

        if($resizewidth_tag && !$resizeheight_tag){
            $ratio = $widthratio;
        }
        if($resizeheight_tag && !$resizewidth_tag){
            $ratio = $heightratio;
        }

        $newwidth = $current_width * $ratio;
        $newheight = $current_height * $ratio;

        if(function_exists("imagecopyresampled"))
        {
            $newim = imagecreatetruecolor($newwidth,$newheight);
           	imagecopyresampled($newim,$im,0,0,0,0,$newwidth,$newheight,$current_width,$current_height);
        }
        else
        {
            $newim = imagecreate($newwidth,$newheight);
           imagecopyresized($newim,$im,0,0,0,0,$newwidth,$newheight,$current_width,$current_height);
        }

        $savename = $savename.$filetype;
        imagejpeg($newim,$savename);
        imagedestroy($newim);
    }
    else
    {
        $savename = $savename.$filetype;
        imagejpeg($im,$savename);
    }           
}
?>