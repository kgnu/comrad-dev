#!/usr/bin/php
<?php

	// Bryan C. Callahan

	require_once(dirname(__FILE__) . '/../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();


	////////////////////////////////////////////////////////////////////////////
	// Clears /all/ schedule events from the ScheduledEvents table...
	function clearEvents($table)
	{
		global $init;

		// Connect to The Kragen!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		$mysqli = new mysqli(
			$init->getProp('Events_Host'), 
			$init->getProp('Events_Username'), 
			$init->getProp('Events_Password'), 
			$init->getProp('Events_Database')
			);

		// Make sure we got a good connection and trucate it up...
		if (mysqli_connect_errno()) die("Can't connect to database\n\n");
		$mysqli->query('TRUNCATE TABLE  `' . $table . '`');
		$mysqli->close();
	}

	// Since this is for DEBUGGING ONLY we're not going to worry about 
	//  prepared statements...
	echo "Clearing all events...\t";
	clearEvents('ScheduledInstances');
	clearEvents('RecurringEventsDaily');
	clearEvents('RecurringEventsWeekly');
	clearEvents('RecurringEventsMonthly');
	clearEvents('RecurringEventsYearly');
	clearEvents('RecurringEventsException');
	echo "Done\n";

	echo "\n";
?>
