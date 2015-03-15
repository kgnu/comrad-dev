<?php
	// This script returns a list of events that are occurring at a specified time
	
	$disableAuthentication = true;
	
	require_once('initialize.php');
	
	PermissionManager::getInstance()->disableAuthorization();
	
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: ' . date('D, d M Y H:i:s') . 'GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');
	header('Content-type: text/x-json');
	
	$dateTime = ($uri->hasKey('datetime') ? $uri->getKey('datetime') : false);
	
	$em = EventManager::getInstance();
	$scheduledEventInstances = $em->getEventsOccurringAtTime(($uri->hasKey('types') ? json_decode($uri->getKey('types'), true) : false), $dateTime);

	$events = array();
	foreach($scheduledEventInstances as $scheduledEventInstance) {
		array_push($events, $scheduledEventInstance->toArray());
	}

	$json = json_encode($events);
	
	echo ($uri->hasKey('callback') ? $uri->getKey('callback') . '(' . $json .')' : $json);
	
	exit();
?>