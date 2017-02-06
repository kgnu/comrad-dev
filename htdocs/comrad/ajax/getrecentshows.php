<?php
	// This script returns a list of events between startDateTime and endDateTime
	// formatted as JSON objects in a format expected by FullCalendar:
	//
	
	$disableAuthentication = true;
	
	require_once('initialize.php');
	
	PermissionManager::getInstance()->disableAuthorization();

	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: ' . date('D, d M Y H:i:s') . 'GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');
	header('Content-type: application/json');
	
	$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	
	$timestamp = time();
	$events = array();
	$startDateTimes = array();
	
	while (count($events) < $limit * $page) {
		$startDateTime = date('Y-m-d H:i:s', $timestamp);
		$timestamp -= 24 * 60 * 60; //get shows over an interval of one day
		$endDateTime = date('Y-m-d H:i:s'); 
		
		$em = EventManager::getInstance();
		$scheduledEventInstances = $em->getEventsBetween(
			$startDateTime,
			$endDateTime,
			array('Show'),
			array('Source' => 'KGNU'),
			FALSE,
			NULL
		);

		foreach($scheduledEventInstances as $scheduledEventInstance) {
			$instance = $scheduledEventInstance->toArray();
			$startDateTime = $instance['Attributes']['StartDateTime'];
			if (!in_array($startDateTime, $startDateTimes)) { //check in_array to prevent duplictes since we are searching back day by day
				array_push($startDateTimes, $startDateTime);
				array_push($events, array(
					'showName' => $instance['Attributes']['ScheduledEvent']['Attributes']['Event']['Attributes']['Title'],
					'showId' => $instance['Attributes']['ScheduledEvent']['Attributes']['Event']['Attributes']['Id'],
					'host' => isset($instance['Attributes']['Host']) && isset($instance['Attributes']['Host']['Attributes']['Name']) ? $instance['Attributes']['Host']['Attributes']['Name'] : '',
					'date' => date('m/d/y', $startDateTime),
					'startTime' => date('g:ia', $startDateTime),
					'archiveUrl' => isset($instance['Attributes']['RecordedFileName']) ? $instance['Attributes']['RecordedFileName'] : '',
					'shortDescription' => isset($instance['Attributes']['ShortDescription']) ? $instance['Attributes']['ShortDescription'] : '',
					'longDescription' => isset($instance['Attributes']['LongDescription']) ? $instance['Attributes']['LongDescription'] : '',
					'permalink' => '/' . $instance['Attributes']['ScheduledEvent']['Attributes']['Event']['Attributes']['URL'] . '/' . date('m/d/y', $startDateTime)
				));
			}
		}
	}
	
	$events = array_slice($events, ($page - 1) * $limit, $limit); //get the specified page and # of events
	
	$json = json_encode($events);
	
	echo ($uri->hasKey('callback') ? $uri->getKey('callback') . '(' . $json .')' : $json);
?>