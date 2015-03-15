<?php
	
	$disableAuthentication = true;
	
	require_once('initialize.php');
	
	PermissionManager::getInstance()->disableAuthorization();
	
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: ' . date('D, d M Y H:i:s') . 'GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');
	header('Content-type: text/x-json');
	
	$results = DB::getInstance('MySql')->find(new Host(), $count, array('sortcolumn' => 'UID', 'limit' => false));
	
	$objects = array();
	foreach ($results as $result) {
		$host = $result->toArray();
		$host = $host['Attributes'];
		array_push($objects, array($host['UID'], $host['Name']));
	}
	
	echo json_encode($objects);
	exit();
	
?>