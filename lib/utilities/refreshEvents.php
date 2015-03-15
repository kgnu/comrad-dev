#!/usr/bin/php
<?php

	// Bryan C. Callahan

	// Determine if the script should serialize output (so web can interpret results)...
	$shouldSerializeOutput = ($_SERVER['argv'][1] == '--serializeOutput');

	// Try to supress ALL errors and serialize them to the caller...
	try
	{
		require_once(dirname(__FILE__) . '/../classes/Initialize.php');
		$init = new Initialize();
		$init->setAutoload();

		// Connect to the event table to attain meta data...
		$events = DB::getInstance('MySql');


		////////////////////////////////////////////////////////////////////////////
		// Inserts events of a given recurring type...
		function insertEvents($type = 'Daily')
		{
			global $init, $shouldSerializeOutput, $events;

			// Create a function header...
			if (!$shouldSerializeOutput) echo "  * Inserting all $type events... \t\t";

			// Loop through all of the recurring events from each of the recurring event tables...
			$reIter = new RecurringEventIterator($type);
			while ($reIter->hasNext())
			{
				// Get the next recurring event rule...
				// Note: if getEndDate() is BEFORE getStartDate() this means that 
				//  the rule repeats forever (we only dump a max of 20 instances 
				//  at a time in the ExpandRecurringIterator.php class).
				$recurringEvent = $reIter->getNext();

				// If we don't have an id, skip it...
				if (!is_numeric($recurringEvent->getID()))
				{
					$init->log('A recurring event rule was found that does not have a valid ID.');
					continue;
				}

				// Query the event's parent...
				$eventParent = new ScheduledInstance($recurringEvent->getParentInstanceID());

				// If the parent event doesn't exist, skip it (we should probably nuke
				//  it to maintain the database)...
				if (!$eventParent->populate())
				{
					// The database could not find the `ScheduledInstances` instance who 
					//  is the parent of this repeating information. Since this repeating 
					//  information is part of the instance we can safely delete this rule 
					//  as cleanup.
					$init->log('While examining the ' . strtolower($type) . ' recurring information (ID: ' . $recurringEvent->getID() . '), the associated parent (ParentInstanceID: ' . $recurringEvent->getParentInstanceID() . ') was determined to be nonexistent. This rule is safe to delete.');

					$id = $recurringEvent->getID();
					$recurringEvent->remove();

					// Try to repopulate the event (make sure it's gone)...
					$dataType = (string) ('RecurringEvent' . $type);
					$testRule = new $dataType($id);

					// Make a note in the log of our success...
					if ($testRule->populate())
						$init->log('The ' . strtolower($type) . ' recurring rule (ID: ' . $id . ') could NOT be removed. Please check the refreshEvents.php utility.');
					else
						$init->log('The ' . strtolower($type) . ' recurring rule (ID: ' . $id . ') has been removed.');

					// Since this event is bad at this point, skip the reset of this iteration...
					continue;
				}

				// Expand the dates given the rules of the recurring event...
				$erIter = new ExpandRecurringIterator($recurringEvent);

				// Loop through each of the resulting dates...
				while ($erIter->hasNext())
				{
					// Snag the next date (as epoch)...
					$nextStartDateTime = $erIter->getNext();

					// Create this new instance in the ScheduledInstances table...
					$event = new ScheduledInstance();
					$event->setEventID($eventParent->getEventID());
					$event->setEventType($eventParent->getEventType());
					$event->setRecurringID($recurringEvent->getID());
					$event->setRecurringType(strtolower($type));
					$event->setStartDateTime($nextStartDateTime);
					$event->setDuration($eventParent->getDuration());
					$event->setIsSafeToRebuild(true);
					$event->update();

					// Make sure the event was saved okay...
					if (!$event->populate())
					{
						$init->log('An instance of the parent event `ScheduledInstances`(' . $recurringEvent->getParentInstanceID() . ') could not be populated after being inserted in the database.');
						continue;
					}
				}
			}

			// Finish up the function with a success!...
			if (!$shouldSerializeOutput) echo "Done\n";
		}

		////////////////////////////////////////////////////////////////////////////
		// Clears /all/ schedule events from the ScheduledInstances table that are 
		//  okay to be rebuilt...
		function clearScheduledInstances()
		{
			global $init, $shouldSerializeOutput;

			if (!$shouldSerializeOutput) echo "  * Clearing all scheduled events... \t\t";

			// Connect to The Kragen!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			$mysqli = new mysqli(
				$init->getProp('Events_Host'), 
				$init->getProp('Events_Username'), 
				$init->getProp('Events_Password'), 
				$init->getProp('Events_Database')
				);

			// Make sure we got a good connection and trucate it up...
			if (mysqli_connect_errno())
			{
				if ($shouldSerializeOutput)
					die(serialize(array(
						'Response' => 'ERROR',
						'ResponseVerbose' => 'Could not connect to the database in order to clear the scheduled instances that are safe to rebuild. Please check the refreshEvents.php utility.'
						)));
				else
					die("Can't connect to database\n\n");
			}
	
			// Query the database to clean up...
			if ($mysqli->query('DELETE FROM `ScheduledInstances` WHERE `SafeToRebuild` = 1;') !== TRUE)
			{
				if ($shouldSerializeOutput)
					die(serialize(array(
						'Response' => 'ERROR',
						'ResponseVerbose' => 'Could not remove the scheduled instances that are safe to rebuild. Please check the refreshEvents.php utility.'
						)));
				else
					die('Could not query the database to remove all `ScheduledInstances` that are `SafeToRebuild`');
			}
	
			// Close up the database...
			if (!$mysqli->close())
			{
				if ($shouldSerializeOutput)
					die(serialize(array(
						'Response' => 'ERROR',
						'ResponseVerbose' => 'Could not remove the scheduled instances that are safe to rebuild. Please check the refreshEvents.php utility.'
						)));
				else
					die('Could not close the database after removing all schedule instances.');
			}
	
			// We're all done cleaning up...
			if (!$shouldSerializeOutput) echo "Done\n\n";
		}


		// Print a header if we're not going to serialize (and give user chance to cancel)...
		if (!$shouldSerializeOutput)
		{
			echo "\n";
			echo "  refreshEvents.php\n\n";
			echo "  This script clears all scheduled events and then recreates them.\n\n";
			echo "  Press [Enter] to continue or Ctrl+C to quit...\n";
			$line = fgets(STDIN);
		}
	
		// Empty out the schedule instances table that are safe to rebuild...
		clearScheduledInstances();
	
		// Insert all the recurring events...
		insertEvents('Daily');
		insertEvents('Weekly');
		insertEvents('Monthly');
		insertEvents('Yearly');
	
		// We don't want the prompt to be on the last line of output...
		// Finish things up...
		if ($shouldSerializeOutput)
	
			echo serialize(array(
				'Response' => 'OK',
				'ResponseVerbose' => 'All instances have been expanded successfully.'
				));

		else

			echo "\n";

	}
	catch (Exception $e)
	{

		if ($shouldSerializeOutput)

			die(serialize(array(
				'Response' => 'ERROR',
				'ResponseVerbose' => 'Could not rebuild all scheduled instances. Please check the refreshEvents.php utility.'
				)));

		else

			die('Caught Exception: ' . $e->getMessage());

	}
	
?>
