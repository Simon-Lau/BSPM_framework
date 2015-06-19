<?php
/*
Uploadify
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
Released under the MIT License <http://www.opensource.org/licenses/mit-license.php> 
*/

// Define a destination
$targetFolder = '/finder/uploads'; // Relative to the root
if (!empty($_FILES)) {
        $fileName = $_POST['mood_pic_id'];
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $targetFolder;
        $last = explode(".", $_FILES['Filedata']['name'])[1];
	$targetFile = rtrim($targetPath,'/') . '/mood_pic_' . $fileName.".".$last;
	// Validate the file type
	$fileTypes = array('jpg','JPG','jpeg','gif','png','PNG','gif'); // File extensions
	$fileParts = pathinfo($_FILES['Filedata']['name']);
	if (in_array($fileParts['extension'],$fileTypes)) {
		move_uploaded_file($tempFile,$targetFile);
		echo '1';
	} else {
		echo 'Invalid file type.';
	}
}
?>