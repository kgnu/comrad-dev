<?php
	require_once('initialize.php');
	
	if ($uri->hasKey('operations') && $uri->hasKey('objects')) {
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . date('D, d M Y H:i:s') . 'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: text/x-json');
		
		echo (PermissionManager::getInstance()->currentUserHasPermissions(json_decode($uri->getKey('operations')), json_decode($uri->getKey('objects'))) ? 'true' : 'false');
		exit();
	}
?>