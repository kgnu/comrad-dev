<?php

	//created sean williams 4/4/11
	//to be run every 30 minutes
	//reads upcoming shows from the database and writes them out to a text file
	//writes info of shows to be recorded into a text file
	
	set_error_handler('errorHandler');
	function errorHandler($errno, $errstr, $errfile, $errline) {
		echo '<br /><b>Error</b>: ' . $errstr . ' (line '.$errline.' in '.$errfile.')<br />';
	}
	
	$root = "../../../"; //the path to the root of the comrad-dev folder
	
	//initialize classes
	require_once($root.'lib/classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();
	
	//get all events in the next day
	$em = EventManager::getInstance();
	$events = $em->getEventsBetween(time(), time() + 60*60*24);
	
	//loop through the next day's events, and prepare a string to store in a text file
	$upcomingEvents = "";
	$startTime = time(); //TODO: make these real values from the database
	$endTime = time() + 400; //TODO: make these real values from the database
	foreach ($events as $scheduledEventInstance) {
		$scheduledEvent = $scheduledEventInstance->ScheduledEvent;
		$event = $scheduledEvent->Event;
		//if we're set up to record
		if ($event->RecordAudio) { //TODO: verify that this is a correct check
			$upcomingEvents .= $event->Title . "|" . $startTime  . "|" . $endTime . "\n";
			$startTime = $startTime + 460; //TODO: make these real values from the database
			$endTime = $endTime + 460; //TODO: make these real values from the database
		}
	}
	
	//write out the upcoming events to a file
	$upcomingEventsFile = "/var/www/comrad-dev-read-only/lib/utilities/archiving/upcomingEvents.txt"; //if updating this, also update the same variable in archiveShows.php
	$fh = fopen($upcomingEventsFile,'w');
	fwrite($fh,$upcomingEvents);
	fclose($fh);
?>
