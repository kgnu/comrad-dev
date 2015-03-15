<?php
	// This script returns a list of events between startDateTime and endDateTime
	// formatted as JSON objects in a format expected by FullCalendar:
	//
	
	$disableAuthentication = true;
	
	require_once('initialize.php');
	
	PermissionManager::getInstance()->disableAuthorization();
	
	if ($uri->hasKey('start') && $uri->hasKey('end')) {
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . date('D, d M Y H:i:s') . 'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: application/json');
		
		$startDateTime = $uri->getKey('start');
		$endDateTime = $uri->getKey('end');
		
		$em = EventManager::getInstance();
		$scheduledEventInstances = $em->getEventsBetween(
			$startDateTime,
			$endDateTime,
			($uri->hasKey('types') ? json_decode($uri->getKey('types'), true) : false),
			($uri->hasKey('eventparameters') ? json_decode($uri->getKey('eventparameters'), true) : false),
			($uri->hasKey('instanceparameters') ? json_decode($uri->getKey('instanceparameters'), true) : false),
			($uri->hasKey('scheduledeventid') ? $uri->getKey('scheduledeventid') : NULL)
		);

		$events = array();
		foreach($scheduledEventInstances as $scheduledEventInstance) {
			if ($uri->hasKey('fullcalendarformat') && $uri->getKey('fullcalendarformat') == true) {
				if ($uri->hasKey('partialcalendardata') && $uri->getKey('partialcalendardata') == 'true') {
					array_push($events, $scheduledEventInstance->toPartialCalendarArray());
				} else {
					array_push($events, $scheduledEventInstance->toFullCalendarArray());
				}
			} else {
				array_push($events, $scheduledEventInstance->toArray());
			}
		}
		
		$json = json_encode($events);
		
		echo ($uri->hasKey('callback') ? $uri->getKey('callback') . '(' . $json .')' : $json);
		
		exit();
	}
?>