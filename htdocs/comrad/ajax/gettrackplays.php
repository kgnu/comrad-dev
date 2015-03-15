<?php
	// This script returns a list of events between startDateTime and endDateTime
	// formatted as JSON objects in a format expected by FullCalendar:
	//
	

	require_once('initialize.php');
	
	if ($uri->hasKey('showid')) {
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . date('D, d M Y H:i:s') . 'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: text/x-json');
		
		$showId = $uri->getKey('showid');
		
		$results = TrackPlayManager::getInstance()->getTrackPlaysForShow($showId);
		
		$trackPlays = array();
		foreach($results as $result) {
			array_push($trackPlays, $result->toArray());
		}
			
		echo json_encode($trackPlays);
		exit();
	}
?>