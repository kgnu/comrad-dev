<?php

	require_once('initialize.php');

	// Check permissions...

	// Execute commands...
	if ($uri->getKeyAsBool('execute')) switch($uri->getKey('cmd'))
	{
		case 'ajaxPopulateEvents':
		
			$jsonEvents = array();
		
			$events = DB::getInstance('MySql');
			$iter = new ScheduledInstanceIterator($uri->getKey('start'), $uri->getKey('end'));
			while ($iter->hasNext())
			{
				$event = $iter->getNext();
				$type = (string)$event->getEventType();

				// If we don't have a proper event type, clean it up...
				if ($type == '')
				{
					$init->log('Event type information for `ScheduledInstance` (ID: ' . $event->getID() . ') was not properly formatted in order to determine metadata.');

					$init->log('Removing the `ScheduledInstance` (ID: ' . $event->getID() . ') instance.');
					$id = $event->getID();
					$event->remove();

					$test = new ScheduledInstance($id);
					if ($test->populate())
						$init->log('The `ScheduledInstance` (ID: ' . $id . ') could NOT be removed. Please check schedule.php?cmd=ajaxPopulateEvents for details.');
					else
						$init->log('The `ScheduledInstance` (ID: ' . $id . ') has been removed.');

					continue;
				}

				// Show is ShowMetadata (we should really change this table to Show)...
				if ($type == 'Show') $type = 'ShowMetadata';

				// Get the event meta information...
				$meta = $events->get(new $type(array( 'UID' => $event->getEventID() )));

				// Make sure that we have valid event information, otherwise clean up...
				if (!is_numeric($meta->UID))
				{
					$init->log('Metadata could not be found for `ScheduledInstance` (ID: ' . $event->getID() . ').');

					$init->log('Removing the `ScheduledInstance` (ID: ' . $event->getID() . ') instance.');
					$id = $event->getID();
					$event->remove();

					$test = new ScheduledInstance($id);
					if ($test->populate())
						$init->log('The `ScheduledInstance` (ID: ' . $id . ') could NOT be removed. Please check schedule.php?cmd=ajaxPopulateEvents for details.');
					else
						$init->log('The `ScheduledInstance` (ID: ' . $id . ') has been removed.');

					continue;
				}

				// Make sure we have some sort of an event title...
				if (is_null($meta->Name) && is_null($meta->Title))
					$title = 'Unknown';
				else
					$title = is_null($meta->Name) ? $meta->Title : $meta->Name;

				// Add this event to the json data...
				$jsonEvents[] = array(
					'title' => $title,
					'start' => date('U', $event->getStartDateTime()),
					'end' => date('U', $event->getStartDateTime() + $event->getDuration()),
					'allDay' => false,
					'className' => "fullCalEvent-$type",

					'Username' => $event->getUsername(),
					'ScheduledInstanceID' => $event->getID(),
					'EventType' => $event->getEventType(),
					'RecurringID' => $event->getRecurringID(),
					'RecurringType' => $event->getRecurringType(),
					'DurationInMins' => round($event->getDuration() / 60.0, 1),
					'DurationInSecs' => $event->getDuration(),
					'Description' => $event->getDescription(),
					'IsScheduled' => $event->getIsScheduled(),
					'IsInstance' => is_null($event->getParent()) && !$event->hasChildren(),
							
					'Active' => $meta->Active,
					'Title' => $meta->Title,
					'Name' => $meta->Name,
					'Copy' => $meta->Copy,
					'Description' => $meta->Description,
					'InternalNote' => $meta->InternalNote
				);
			}

			echo json_encode($jsonEvents);
	
			exit();

		case 'ajaxGetEventSelectOptions':

			$type = (string) $uri->getKey('eventType');
			$events = DB::getInstance('MySql');
			$eventsData = $events->find(new $type(), $count);

			// If we have any results, build some HTML...
			if ($count > 0) foreach ($eventsData as $event)
			{
				$name = is_null($event->Name) ? $event->Title : $event->Name;
				echo '<option value="' . $event->UID . '"';
				if ($event->UID == $uri->getKey('id')) echo ' selected="selected"';
				echo '>' . $name . '</option>';
			}
		
			exit();

		case 'ajaxGetInstanceInformation':

			$instance = new ScheduledInstance($uri->getKey('ScheduledInstanceID'));
			$instance_exists = $instance->populate();
			$recurring_instance = $instance->getRecurringEvent();

			// Make sure the instance exists...
			if (!$instance_exists)
			{
				die(json_encode(array(
					'Response' => 'ERROR',
					'ResponseVerbose' => 'The instance the not exist. Please try refreshing the page.'
					)));
			}

			// Compile ajax response variables...
			$ret = array(
				'Response' => 'OK',
				'ResponseVerbose' => 'The instance was populated.',
				);

			// Compile instance variables...
			$ret = array_merge($ret, array(
				'ID' => $instance->getID(),
				'eventID' => $instance->getEventID(),
				'eventType' => $instance->getEventType(),
				'startDate' => date('n/j/Y', $instance->getStartDateTime()),
				'startTime' => date('g:i A', $instance->getStartDateTime()),
				'endTime' => date('g:i A', $instance->getStartDateTime() + $instance->getDuration()),
				'endDate' => date('n/j/Y', $instance->getStartDateTime() + $instance->getDuration()),
				'doesRepeat' => is_null($recurring_instance) ? 0 : 1,
				'IsSafeToRebuild' => $instance->getIsSafeToRebuild() ? 1 : 0,
				'IsParent' => $instance->getIsParent() ? 1 : 0
				));

			// If we don't repeat, we know we don't have exceptions...
			if (is_null($recurring_instance)) $ret = array_merge($ret, array('doesHaveExceptions' => 0));

			// Compile recurring variables...
			if (!is_null($recurring_instance))
			{
				$parent = $instance->getParent();
				if (!is_null($parent))
				{
					$ret = array_merge($ret, array(
						'ParentInstanceID' => $parent->getID(),
						'ParentInstanceEventType' => $parent->getEventType(),
						'ParentInstanceStartDate' => date('n/j/Y', $parent->getStartDateTime()),
						'ParentInstanceStartTime' => date('g:i A', $parent->getStartDateTime()),
						'ParentInstanceEndTime' => date('g:i A', $parent->getStartDateTime() + $parent->getDuration()),
						'ParentInstanceEndDate' => date('n/j/Y', $parent->getStartDateTime() + $parent->getDuration())
						));
				}

				$ret = array_merge($ret, array('repeatType' => $instance->getRecurringType()));

				switch ($instance->getRecurringType())
				{
					case 'daily': $ret = array_merge($ret, array('repeatsEvery' => $recurring_instance->getEveryXDays())); break;
					case 'weekly': $ret = array_merge($ret, array('repeatsEvery' => $recurring_instance->getEveryXWeeks())); break;
					case 'monthly': $ret = array_merge($ret, array('repeatsEvery' => $recurring_instance->getEveryXMonths())); break;
					case 'yearly': $ret = array_merge($ret, array('repeatsEvery' => $recurring_instance->getEveryXYears())); break;
					default: $ret = array_merge($ret, array('repeatsEvery' => 1)); break;
				}

				$ret = array_merge($ret, array(
					'until' => date('n/j/Y', $recurring_instance->getEndDate()),
					'endsOn' => ($recurring_instance->getStartDate() > $recurring_instance->getEndDate()) ? 'never' : 'until'
					));

				if ($instance->getRecurringType() == 'weekly')
				{
					$ret = array_merge($ret, array(
						'weekdaySunday' => $recurring_instance->getIsSunday() ? 1 : 0,
						'weekdayMonday' => $recurring_instance->getIsMonday() ? 1 : 0,
						'weekdayTuesday' => $recurring_instance->getIsTuesday() ? 1 : 0,
						'weekdayWednesday' => $recurring_instance->getIsWednesday() ? 1 : 0,
						'weekdayThursday' => $recurring_instance->getIsThursday() ? 1 : 0,
						'weekdayFriday' => $recurring_instance->getIsFriday() ? 1 : 0,
						'weekdaySaturday' => $recurring_instance->getIsSaturday() ? 1 : 0
						));
				}
				else
				{
					$ret = array_merge($ret, array(
						'weekdaySunday' => 0,
						'weekdayMonday' => 0,
						'weekdayTuesday' => 0,
						'weekdayWednesday' => 0,
						'weekdayThursday' => 0,
						'weekdayFriday' => 0,
						'weekdaySaturday' => 0
						));
				}

				if ($instance->getRecurringType() == 'monthly')
					$ret = array_merge($ret, array('repeatBy' => $recurring_instance->getRepeatBy()));
				else
					$ret = array_merge($ret, array('repeatBy' => 'dayOfMonth'));

				// Determine if there are exception from the exString...
				$exString = RecurringEventException::generateExceptionString($instance->getRecurringID(), $instance->getRecurringType());
				$ret = array_merge($ret, array('doesHaveExceptions' => ($exString == '') ? 0 : 1));
				$ret = array_merge($ret, array('exceptionString' => $exString));
			}
			else
			{
				$ret = array_merge($ret, array());
			}

			// Output the sweeet json...
			echo json_encode($ret);
			exit();


		case 'ajaxModifyEvent':
		case 'ajaxScheduleEvent':

			$events = DB::getInstance('MySql');

			// Shortcuts to a unix epoch...
			$startTime = strtotime($uri->getKey('startDate') . ' ' . $uri->getKey('startTime'));
			$endTime = strtotime($uri->getKey('endDate') . ' ' . $uri->getKey('endTime'));

			// Check to make sure the $startTime and $endTime are properly formatted...
			if ($startTime === false || $endTime === false || $uri->getKey('startTime') == '' || $uri->getKey('endTime') == '')
				die(json_encode(array(
					'Response' => 'ERROR',
					'ResponseVerbose' => 'Please make sure the "When" field is properly formatted.'
					)));

			// Make sure the $startTime is before the $endTime...
			if ($endTime < $startTime)
				die(json_encode(array(
					'Response' => 'ERROR',
					'ResponseVerbose' => 'Please make sure the "When" field is properly formatted. The end date and time should occur AFTER the start date and time.'
					)));

			// Make sure that we can only have specific repeating types...
			if ( ($uri->getKey('doesRepeat') == '1') && 
				(
					$uri->getKey('repeatType') != 'daily' && 
					$uri->getKey('repeatType') != 'weekly' && 
					$uri->getKey('repeatType') != 'monthly' && 
					$uri->getKey('repeatType') != 'yearly')
				)
				die(json_encode(array(
					'Response' => 'ERROR',
					'ResponseVerbose' => 'When adding Repeat details, please make sure to choose whether the event is to repeat Daily, Weekly, Monthly, or Yearly.'
					)));

			// Make sure that the exception checkbox is ONLY checked if a repeating event...
			if ( ($uri->getKey('hasExceptions') == '1') && ($uri->getKey('doesRepeat') == '0') )
				die(json_encode(array(
					'Response' => 'ERROR',
					'ResponseVerbose' => 'Please remember that Exceptions are only available if repeating event details are specified. Is "Exceptions..." checked and "Repeats..." unchecked?'
					)));

			// If the event is repeating, make sure that the "Repeat Every" and "Ends On" fields are valid...
			if ($uri->getKey('doesRepeat') == '1')
			{
				// Make sure we have a valid repeat every field...
				if (!is_numeric($uri->getKey('repeatsEvery')) || $uri->getKey('repeatsEvery') < 1)
				{
					$per = 'once per ';
					switch ($uri->getKey('repeatType'))
					{
						case 'daily': $per .= 'day'; break;
						case 'weekly': $per .= 'week'; break;
						case 'monthly': $per .= 'month'; break;
						case 'yearly': $per .= 'year'; break;
					}
					die(json_encode(array(
						'Response' => 'ERROR',
						'ResponseVerbose' => 'Please make sure that your ' . $uri->getKey('repeatType') . ' repeating event details are correct. This repeat type must occur at least ' . $per . '.'
						)));
				}

				// Ends on must be never or until...
				if ( ($uri->getKey('endsOn') != 'never') && ($uri->getKey('endsOn') != 'until') )
					die(json_encode(array(
						'Response' => 'ERROR',
						'ResponseVerbose' => 'Please make sure that your ' . $uri->getKey('repeatType') . ' repeating event details are correct. This repeat type must either never end or end on a specific date.'
						)));

				// If the event ends on is "until" we must have a parseable date...
				if ( ($uri->getKey('endsOn') == 'until') && (strtotime($uri->getKey('until')) === false) )
					die(json_encode(array(
						'Response' => 'ERROR',
						'ResponseVerbose' => 'Please make sure that your ' . $uri->getKey('repeatType') . ' repeating event details are correct. This repeat type must end on a specific date. Does your "Ends On" field have a valid "Until" date?'
						)));

				// Check weekly specifics...
				if ($uri->getKey('repeatType') == 'weekly')
				{
					// Make sure that our event occurs at least once per weekday...
					if ( ($uri->getKey('weekdaySunday') != '1') && 
						($uri->getKey('weekdayMonday') != '1') && 
						($uri->getKey('weekdayTuesday') != '1') && 
						($uri->getKey('weekdayWednesday') != '1') && 
						($uri->getKey('weekdayThursday') != '1') && 
						($uri->getKey('weekdayFriday') != '1') && 
						($uri->getKey('weekdaySaturday') != '1') )
						die(json_encode(array(
							'Response' => 'ERROR',
							'ResponseVerbose' => 'Please make sure that your weekly repeating event details are correct. This repeat type must occur at least once per week. Did you check a weekday?'
							)));
				}

				// Check monthly specifics...
				if ($uri->getKey('repeatType') == 'monthly')
				{
					if ( ($uri->getKey('repeatBy') != 'dayOfMonth') && ($uri->getKey('repeatBy') != 'dayOfWeek') )
						die(json_encode(array(
							'Response' => 'ERROR',
							'ResponseVerbose' => 'Please make sure that your monthly repeating event details are correct. This repeat type must repeat by the day of the month (e.g. every second day of the month) or by the day of the week (e.g. every second thursday of the month). Did you make sure to choose an option for the "Repeat By" field?'
							)));
				}

				// Check exception specifics...
				if ($uri->getKey('hasExceptions') == '1')
				{
					// Is there an exception on the end day?
					if ($uri->getKey('exceptions') != '')
					{
						$exDates = explode(',', $uri->getKey('exceptions'));
						if (in_array($uri->getKey('until'), $exDates)) die(json_encode(array(
							'Response' => 'ERROR',
							'ResponseVerbose' => 'Exceptions cannot be created on the last day of the recurrance.'
							)));
					}
				}
			}


			// Compile the event information...
			$type = (string) $uri->getKey('eventType');
			$meta = $events->get(new $type(array( 'UID' => $uri->getKey('eventID') )));

			// -- WE SHOULD RENAME 'ShowMetadata' => 'Show' --
			if ($uri->getKey('eventType') == 'ShowMetadata') $uri->updateKey('eventType', 'Show');

			// Make sure we have a valid UID. If we don't, we have an invalid event we cannot schedule...
			if (!is_numeric($meta->UID))
				die(json_encode(array(
					'Response' => 'ERROR',
					'ResponseVerbose' => 'The event you selected could not be scheduled. Please check to make sure the event is correctly populated in event management.'
					)));

			// Schedule the instance (or parent event if we're recurring)...
			$instance = new ScheduledInstance($uri->getKey('instanceID'));
			$instance_exists = $instance->populate();

			// Make sure that if we're modifying that the instance exists...
			if ( ($uri->getKey('cmd') == 'ajaxModifyEvent') && !$instance_exists )
				die(json_encode(array(
					'Response' => 'ERROR',
					'ResponseVerbose' => 'The event you are trying to modify does not exist. Please select an event from the schedule.'
					)));

			// Update the basic member variables...
			$instance->setUsername($_SESSION['Username']);
			$instance->setStartDateTime($startTime);
			$instance->setDuration($endTime - $startTime);
			$instance->setEventID($uri->getKey('eventID'));
			$instance->setEventType($uri->getKey('eventType'));
			$instance->setIsParent($uri->getKeyAsBool('doesRepeat'));
			$instance->update();

			// We can only break events apart if we're modifying them so check that first.
			//  If we are modifying, then determine if we need to perform a break...
			if ( ($uri->getKey('cmd') == 'ajaxModifyEvent') && ($instance->getRecurringType() != '') && $uri->getKeyAsBool('breakInstance') )
			{
				// Add an exception for the series (so it doesn't recreate something here)...
				$ex_instance = new RecurringEventException();
				$ex_instance->setRecurringID($instance->getRecurringID());
				$ex_instance->setRecurringType($instance->getRecurringType());
				$ex_instance->setStartDateTime($instance->getStartDateTime());
				$ex_instance->setEndDateTime($instance->getStartDateTime() + $instance->getDuration());
				$ex_instance->update();

				// Convert this instance into a nonseries instance...
				$instance->setIsSafeToRebuild(false);
				$instance->setRecurringID(0);
				$instance->setRecurringType('');
				$instance->update();
			}

			// Repopulate the event to make sure it was created...
			if (!$instance->populate())
				die(json_encode(array(
					'Response' => 'ERROR',
					'Response' => 'The event you are attempting to schedule could not be created because the server is unable to communicate with the database.'
					)));

			// If we changed recurring types, nuke old repeat info...
			if ( ($uri->getKey('cmd') == 'ajaxModifyEvent') && ($uri->getKey('repeatType') != $instance->getRecurringType()) )
			{
				$old_rep_instance = $instance->getRecurringEvent();
				if (!is_null($old_rep_instance)) $old_rep_instance->remove();
			}

			// Create recurrance rules (if user provides repeat info)...
			if ($uri->getKey('doesRepeat')) switch ($uri->getKey('repeatType'))
			{
				case 'daily':

					if ($instance->getRecurringType() == 'daily')
						$event = $instance->getRecurringEvent();
					else
						$event = new RecurringEventDaily();

					$event->setParentInstanceID($instance->getID());
					$event->setStartDate($startTime);
					$event->setEndDate($uri->getKey('until'));
					$event->setEveryXDays($uri->getKey('repeatsEvery'));
					$event->update();

					$instance->setRecurringID($event->getID());

					// Make sure we can repopulate the event information...
					if (!$event->populate())
					{
						$instance->remove();
						die(json_encode(array(
							'Response' => 'ERROR',
							'Response' => 'The recurring event you are attempting to schedule could not be created because the server is unable to communicate with the database.'
							)));
					}

					break;

				case 'weekly':

					if ($instance->getRecurringType() == 'weekly')
						$event = $instance->getRecurringEvent();
					else
						$event = new RecurringEventWeekly();

					$event->setParentInstanceID($instance->getID());
					$event->setStartDate($startTime);
					$event->setEndDate($uri->getKey('until'));
					$event->setEveryXWeeks($uri->getKey('repeatsEvery'));
					$event->setIsSunday($uri->getKeyAsBool('weekdaySunday'));
					$event->setIsMonday($uri->getKeyAsBool('weekdayMonday'));
					$event->setIsTuesday($uri->getKeyAsBool('weekdayTuesday'));
					$event->setIsWednesday($uri->getKeyAsBool('weekdayWednesday'));
					$event->setIsThursday($uri->getKeyAsBool('weekdayThursday'));
					$event->setIsFriday($uri->getKeyAsBool('weekdayFriday'));
					$event->setIsSaturday($uri->getKeyAsBool('weekdaySaturday'));
					$event->update();

					// Make sure we can repopulate the event information...
					if (!$event->populate())
					{
						$instance->remove();
						die(json_encode(array(
							'Response' => 'ERROR',
							'Response' => 'The recurring event you are attempting to schedule could not be created because the server is unable to communicate with the database.'
							)));
					}

					break;

				case 'monthly':

					if ($instance->getRecurringType() == 'monthly')
						$event = $instance->getRecurringEvent();
					else
						$event = new RecurringEventMonthly();

					$event->setParentInstanceID($instance->getID());
					$event->setStartDate($startTime);
					$event->setEndDate($uri->getKey('until'));
					$event->setEveryXMonths($uri->getKey('repeatsEvery'));
					$event->setRepeatBy($uri->getKey('repeatBy'));
					$event->update();

					// Make sure we can repopulate the event information...
					if (!$event->populate())
					{
						$instance->remove();
						die(json_encode(array(
							'Response' => 'ERROR',
							'Response' => 'The recurring event you are attempting to schedule could not be created because the server is unable to communicate with the database.'
							)));
					}

					break;

				case 'yearly':

					if ($instance->getRecurringType() == 'yearly')
						$event = $instance->getRecurringEvent();
					else
						$event = new RecurringEventYearly();

					$event->setParentInstanceID($instance->getID());
					$event->setStartDate($startTime);
					$event->setEndDate($uri->getKey('until'));
					$event->setEveryXYears($uri->getKey('repeatsEvery'));
					$event->update();

					// Make sure we can repopulate the event information...
					if (!$event->populate())
					{
						$instance->remove();
						die(json_encode(array(
							'Response' => 'ERROR',
							'Response' => 'The recurring event you are attempting to schedule could not be created because the server is unable to communicate with the database.'
							)));
					}

					break;
			}

			// Make sure that (if we're repeating) that the parent event has 
			//  the recurring information. Also make sure that we have properly 
			//  created exceptions...
			if ($uri->getKeyAsBool('doesRepeat'))
			{
				$instance->setRecurringID($event->getID());
				$instance->setRecurringType($uri->getKey('repeatType'));
				$instance->update();

				// Loop through all the exceptions and add them...
				if ($uri->getKeyAsBool('hasExceptions'))
				{
					// Remove all of the associated exceptions...
					RecurringEventException::removeAllExceptions($event->getID(), $uri->getKey('repeatType'));
	
					// Loop through the exceptions to recreate them...
					if ($uri->getKey('exceptions') != '')
					{
						$exDates = explode(',', $uri->getKey('exceptions'));
						foreach ($exDates as $exDate)
						{
							$dateAsTime = strtotime($exDate);
	
							$dateAsTime = mktime(
								date('H', $startTime), date('i', $startTime), date('s', $startTime),
								date('n', $dateAsTime), date('j', $dateAsTime), date('Y', $dateAsTime)
								);
	
							// This doesn't exist, add it...
							$ex_instance = new RecurringEventException();
							$ex_instance->setRecurringID($event->getID());
								$ex_instance->setRecurringType($uri->getKey('repeatType'));
							$ex_instance->setStartDateTime($dateAsTime);
							$ex_instance->setEndDateTime($dateAsTime + $instance->getDuration());
							$ex_instance->update();
						}
					}
				}

			}

			// Make sure we force a content refresh (this updates the ScheduledInstances table)...
			$output = shell_exec($init->getProp('Admin_Utilities_Path') . '/refreshEvents.php --serializeOutput');
			$output = unserialize($output);

			// If the output is null (we couldn't serialize) then make a generic error message...
			if ($output === false) $output = array(
				'Response' => 'ERROR',
				'ResponseVerbose' => 'Could not rebuild all scheduled instances. Please check the refreshEvents.php utility.'
				);

			// Let the user know if any problems have occured while rebuilding...
			if ($output['Response'] != 'OK')
				die(json_encode(array(
					'Response' => 'ERROR',
					'ResponseVerbose' => $output['ResponseVerbose']
					)));

			// Compile return message...
			echo json_encode(array(
				'Response' => 'OK',
				'ResponseVerbose' => 'Added event to schedule'
				));

			exit();


		case 'ajaxRemoveEvent':

			$instance = new ScheduledInstance($uri->getKey('instanceID'));
			$instance_exists = $instance->populate();

			// Make sure we have a valid instance id...
			if (!$instance_exists)
			{
				die(json_encode(array(
					'Response' => 'ERROR',
					'ResponseVerbose' => 'The instance does not exist. Please try refreshing the page.'
					)));
			}

			// Nuke recurring info if we have it...
			$recurringInfo = $instance->getRecurringEvent();
			if (!is_null($recurringInfo)) $recurringInfo->remove();

			// Nuke the instance...
			$instance->remove();
			
			// Make sure we force a content refresh (this updates the ScheduledInstances table)...
			$output = shell_exec($init->getProp('Admin_Utilities_Path') . '/refreshEvents.php --serializeOutput');
			$output = unserialize($output);

			// If the output is null (we couldn't serialize) then make a generic error message...
			if ($output === false) $output = array(
				'Response' => 'ERROR',
				'ResponseVerbose' => 'Could not rebuild all scheduled instances. Please check the refreshEvents.php utility.'
				);

			// Let the user know if any problems have occured while rebuilding...
			if ($output['Response'] != 'OK')
				die(json_encode(array(
					'Response' => 'ERROR',
					'ResponseVerbose' => $output['ResponseVerbose']
					)));

			// Compile return message...
			echo json_encode(array(
				'Response' => 'OK',
				'ResponseVerbose' => 'The instance has been removed.'
				));

			exit();
	}
?>
<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();                                   # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Schedule</title>

	<link type="text/css" rel="stylesheet" href="css/jquery/fullcalendar/fullcalendar.css" />
	
	<script type='text/javascript' src='js/jquery/fullcalendar/fullcalendar.js'></script>
	<script type="text/javascript" src="js/jquery/qtip/jquery.qtip.js"></script>
	
	<script type='text/javascript' src='js/date/format/date.format.js'></script>

	<script type="text/javascript"> 
	/* <![CDATA[ */

	// Array that holds a list of date exceptions for the given event...
	var eventExceptions = new Array();


	////////////////////////////////////////////////////////////////////////////////
	function populateEventInfo(event, breakInstance)
	{
		$.post('schedule.php', {cmd: 'ajaxGetInstanceInformation', execute: '1', ScheduledInstanceID: event.ScheduledInstanceID}, function (data)
		{
			resetDialogCreateModify();

			$('#dialogCreateModify').dialog('option', 'title', 'Modify a Scheduled Event');
			$('#dialogCreateModify').dialog('option', 'buttons', {
				Cancel: dialogCreateModify_cancel,
				'Modify': dialogCreateModify_modify,
				'Remove': function() { dialogCreateModify_remove(event); }
			});					

			$('#shouldBreakInstance').val(breakInstance ? 1 : 0);

			if (breakInstance)
			{
				$('#instanceID').val(data.ID);			// Empty string = Create, id = Edit
				$('#eventType').val(data.eventType);
				showEvents(data.eventID);
				$('#startDate').val(data.startDate);
				$('#startTime').val(data.startTime);
				$('#endTime').val(data.endTime);
				$('#endDate').val(data.endDate);
			}
			else
			{
				$('#instanceID').val(data.ParentInstanceID);			// Empty string = Create, id = Edit
				$('#eventType').val(data.ParentInstanceEventType);
				showEvents(data.eventID);
				$('#startDate').val(data.ParentInstanceStartDate);
				$('#startTime').val(data.ParentInstanceStartTime);
				$('#endTime').val(data.ParentInstanceEndTime);
				$('#endDate').val(data.ParentInstanceEndDate);

				$('#doesRepeat').attr('checked', data.doesRepeat);
				$('#repeatType').val(data.repeatType);
				$('#repeatsEvery').val(data.repeatsEvery);
				$('#weekdaySunday').attr('checked', data.weekdaySunday);
				$('#weekdayMonday').attr('checked', data.weekdayMonday);
				$('#weekdayTuesday').attr('checked', data.weekdayTuesday);
				$('#weekdayWednesday').attr('checked', data.weekdayWednesday);
				$('#weekdayThursday').attr('checked', data.weekdayThursday);
				$('#weekdayFriday').attr('checked', data.weekdayFriday);
				$('#weekdaySaturday').attr('checked', data.weekdaySaturday);
				$('#doesRepeatRow_monthDetailsRow input:radio[name=repeatBy]').filter('[value=' + data.repeatBy + ']').attr('checked', true);
				$('#doesRepeatRow_endsOnRow input:radio[name=endsOn]').filter('[value=' + data.endsOn + ']').attr('checked', true);
				$('#until').val(data.until);

				$('#hasExceptions').attr('checked', data.doesHaveExceptions);
				if (data.doesHaveExceptions)
				{
					eventExceptions = data.exceptionString.split(',');
					buildExceptionUI();
				}
			}

			updateRepeatsInterface();
			toggleRepeat();

			$('#dialogCreateModify').dialog('open');
						
		}, 'json');
	}

	////////////////////////////////////////////////////////////////////////////////
	// This event fires when a user single clicks an event...
	function calendar_eventClick(event)
	{
		// If normal instance, modify it. Otherwise ask the user what they 
		//  wanna do...
		if (event.IsInstance)
		{
			populateEventInfo(event, true);
		}
		else
		{
			var buttons = {
				'Cancel': function() { var func = $(this).dialog('option', 'func'); func(3); $(this).dialog('close'); },
				'All events': function() { var func = $(this).dialog('option', 'func'); func(2); $(this).dialog('close'); },
				'Only this event': function() { var func = $(this).dialog('option', 'func'); func(1); $(this).dialog('close'); }
				};

			var msg = 'Would you like to modify <i>this event</i> or <i>all events</i> in this series?';

			// Ask the user if he or she would like to break the instance...
			confirmDialog('Modify Recurring Event', msg, buttons, function(ret) {

				var breakInstance = (ret == 1) ? 1 : 0;

				if (ret != 1 && ret != 2)
				{
					$('#calendar').fullCalendar('refetchEvents');
					return;
				}

				populateEventInfo(event, breakInstance);
			});
		}
	}

	////////////////////////////////////////////////////////////////////////////////
	function initFullCalendar(slotMinutes)
	{
		if (slotMinutes == undefined) slotMinutes = 30;

		$('#calendar').fullCalendar({

			header: {
				left: 'title', 
				right: 'month,agendaWeek,agendaDay basicWeek,basicDay today prev,next'
				},

			buttonText: {
				month: 'Month',
				agendaWeek: 'Week',
				agendaDay: 'Day',
				basicWeek: 'Weekly Agenda',
				basicDay: 'Daily Agenda'
				},

			slotMinutes: slotMinutes,
			defaultEventMinutes: 1,
			firstHour: <?php echo date('H'); ?>,
			allDaySlot: false,

			defaultView: 'agendaWeek', 
			editable: false, 
			events: "schedule.php?cmd=ajaxPopulateEvents&execute=1", 

			loading: function(isLoading, view)
				{
					if (isLoading)
						$('#calendarLoading').show();
					else
						$('#calendarLoading').hide();
				},

			eventRender: function(event, element, view)
				{
					if (!event.IsInstance)
						element.children().html(element.children().html() + '<img src="media/icon-calendar-series.png" style="margin: 2px 2px 3px 2px; vertical-align: middle;" />');

					var content = "<div class=\"songTip\">";
					content += "<div class=\"songTipTitle\">" + event.title + "</div>";

					content += "<div class=\"songTipRow\"><div class=\"songTipField\">Type:</div><div class=\"songTipDescription\">" + event.EventType + "</div></div>";
					content += "<div class=\"songTipRow\"><div class=\"songTipField\">Duration:</div><div class=\"songTipDescription\">" + event.DurationInMins + " mins</div></div>";
					content += "<div class=\"songTipRow\"><div class=\"songTipField\">Repeats:</div><div class=\"songTipDescription\">" + ((event.IsInstance) ? "No" : "Yes") + "</div></div>";
					content += "<div class=\"songTipRow\"><div class=\"songTipField\">Active:</div><div class=\"songTipDescription\">" + ((event.Active) ? "Yes" : "No") + "</div></div>";

					if (event.Title != null) content += "<div class=\"songTipRow\"><div class=\"songTipField\">Title:</div><div class=\"songTipDescription\">" + event.Title + "</div></div>";
					if (event.Name != null) content += "<div class=\"songTipRow\"><div class=\"songTipField\">Name:</div><div class=\"songTipDescription\">" + event.Name + "</div></div>";
					if (event.Copy != null) content += "<div class=\"songTipRow\"><div class=\"songTipField\">Copy:</div><div class=\"songTipDescription\">" + event.Copy + "</div></div>";
					if (event.Description != null) content += "<div class=\"songTipRow\"><div class=\"songTipField\">Description:</div><div class=\"songTipDescription\">" + event.Description + "</div></div>";
					if (event.InternalNote != null) content += "<div class=\"songTipRow\"><div class=\"songTipField\">Internal Note:</div><div class=\"songTipDescription\">" + event.InternalNote + "</div></div>";

					content += "</div>";

					element.qtip({
						show: { solo: true },
						content: content,
						position: {corner: {tooltip: 'topMiddle', target: 'bottomMiddle'}},
						style: {
							border: {width: 5, radius: 10},
							padding: 10,
							tip: true,
							name: 'dark'
						}
					});
				},

			dayClick: function(date, allDay, jsEvent, view)
				{
					var startDate = date.format('m/d/yyyy');
					var startTime = date.format('h:MM TT');

					resetDialogCreateModify();
					$('#startDate').val(startDate);
					$('#startTime').val(startTime);
					$('#endTime').val(startTime);
					$('#endDate').val(startDate);
					$('#dialogCreateModify').dialog('open');
				},

			eventClick : calendar_eventClick

		});

	}

	////////////////////////////////////////////////////////////////////////////////
	function changeFullCalSlotMins(slotMinutes)
	{
		// http://code.google.com/p/fullcalendar/issues/detail?id=293&can=4
//		$('#calendar').fullCalendar('destroy');
		$('#calendar').html('');
		initFullCalendar(slotMinutes);
	}

	////////////////////////////////////////////////////////////////////////////////
	$(function() {

		$('#calendarLoading').hide();
		initFullCalendar();

		$('#slotMinutesSlider').slider({
			min: 1,
			max: 60,
			value: 30,
			slide: function(event, ui) {
				$('#slotMinutesSliderValue').html(ui.value + ' mins');
			},
			stop: function(event, ui) {
				$('#slotMinutesSliderValue').html(ui.value + ' mins');
				changeFullCalSlotMins(ui.value)
			}
		});

		$('#dialogCreateModify').dialog({
			autoOpen:		false,
			resizable:		false,
			modal:			true,
			width:			480,
			show:			'puff',
			hide:			'puff'
		});

		$('#dialogConfirm').dialog({
			title:			'Are you sure?',
			autoOpen:		false,
			resizable:		false,
			modal:			true,
			width:			380,
			show:			'puff',
			hide:			'puff',
			buttons:		{
				Cancel: function() { var func = $(this).dialog('option', 'func'); func(1); $(this).dialog('close'); },
				'Schedule': function() { var func = $(this).dialog('option', 'func'); func(2); $(this).dialog('close'); }
			}
		});

		$('#startDate').datepicker({dateFormat: 'm/d/yy', showButtonPanel: true, changeMonth: false, changeYear: false});
		$('#endDate').datepicker({dateFormat: 'm/d/yy', showButtonPanel: true, changeMonth: false, changeYear: false});
		$('#until').datepicker({dateFormat: 'm/d/yy', showButtonPanel: true, changeMonth: false, changeYear: false});
		$('#exceptionOn').datepicker({dateFormat: 'm/d/yy', showButtonPanel: true, changeMonth: false, changeYear: false});

		resetDialogCreateModify();

		$('#btnAddException').click(btnAddException_click);
	});


	////////////////////////////////////////////////////////////////////////////
	function removeException(exDate)
	{
		// If we're not enabled, bail...
		if (!$('#hasExceptions').attr('checked')) return false;

		// Look through all the exceptions...
		for (var i = 0; i < eventExceptions.length; i++)
		{
			// See if we can find the exception to remove it...
			if (eventExceptions[i] == exDate)
			{
				eventExceptions.splice(i, 1);
				buildExceptionUI();
				return true;	// We removed the exception
			}
		}

		return false;		// We didn't remove an exception
	}

	////////////////////////////////////////////////////////////////////////////
	function buildExceptionUI()
	{
		var html = '';

		for (var i = 0; i < eventExceptions.length; i++)
		{
			html += '<span class="fg-button ui-state-default ui-corner-all exception">';
			html += '<span class="ui-icon ui-icon-circle-minus exceptionRemove" onclick="removeException(\'' + eventExceptions[i] + '\');"></span>';
			html += '<span class="exceptionDate">' + eventExceptions[i] + '</span>';
			html += '</span>';
		}

		$('#exceptionContainer').html(html);
	}

	////////////////////////////////////////////////////////////////////////////
	function addException(exDate)
	{
		// Bail if we don't have a date to add...
		if (exDate == null) return;

		// We only care about the date...
		exDate = exDate.format('m/d/yyyy');

		// Make sure this isn't already included...
		for (var i = 0; i < eventExceptions.length; i++)
		{
			if (eventExceptions[i] == exDate)
			{
				alert('This exception already exists.');
				return;
			}
		}

		// Add the exception to the array...
		eventExceptions.push(exDate);
		buildExceptionUI();
	}

	////////////////////////////////////////////////////////////////////////////
	function btnAddException_click()
	{
		addException($('#exceptionOn').datepicker('getDate'));
	}

	////////////////////////////////////////////////////////////////////////////
	function resetDialogCreateModify()
	{
		$('#dialogCreateModify').dialog('option', 'title', 'Schedule an Event');
		$('#dialogCreateModify').dialog('option', 'buttons', {
			Cancel: dialogCreateModify_cancel,
			'Schedule': dialogCreateModify_schedule
		});

		$('#shouldBreakInstance').val('');
		$('#instanceID').val('');			// Empty string = Create, id = Edit
		$('#eventType').val('Please select an event type...');
		$('#events').html('');
		$('#startDate').val('');
		$('#startTime').val('');
		$('#endTime').val('');
		$('#endDate').val('');
		$('#doesRepeat').attr('checked', false);
		$('#repeatType').val('daily');
		$('#repeatsEvery').val('1');
		$('#weekdaySunday').attr('checked', false);
		$('#weekdayMonday').attr('checked', false);
		$('#weekdayTuesday').attr('checked', false);
		$('#weekdayWednesday').attr('checked', false);
		$('#weekdayThursday').attr('checked', false);
		$('#weekdayFriday').attr('checked', false);
		$('#weekdaySaturday').attr('checked', false);
		$('#doesRepeatRow_monthDetailsRow input:radio[name=repeatBy]').filter('[value=dayOfMonth]').attr('checked', true);
		$('#doesRepeatRow_endsOnRow input:radio[name=endsOn]').filter('[value=never]').attr('checked', true);
		$('#until').val('');

		$('#hasExceptions').attr('checked', false);
		eventExceptions = new Array();
		$('#exceptionContainer').html('');
		$('#exceptionOn').val('');
		
		updateRepeatsInterface();
		toggleRepeat();
	}

	////////////////////////////////////////////////////////////////////////////
	function confirmDialog(title, message, buttons, func)
	{
		$('#dialogConfirm').dialog('option', 'buttons', buttons);
		$('#dialogConfirm').dialog('option', 'func', func);
		$('#dialogConfirm').dialog('option', 'title', title);
		$('#dialogConfirm_message').html(message);
		$('#dialogConfirm').dialog('open');
	}

	////////////////////////////////////////////////////////////////////////////
	function dialogCreateModify_cancel()
	{
		$('#dialogCreateModify').dialog('close');
	}

	////////////////////////////////////////////////////////////////////////////
	function dialogCreateModify_schedule()
	{
		if ($('#eventID').val() == '' || $('#eventID').val() == undefined) { alert('Please specify an event.'); return; }

		var params = {
			cmd: 'ajaxScheduleEvent',
			execute: 1,
			eventID: $('#eventID').val(),
			eventType: $('#eventType').val(),
			startDate: $('#startDate').val(),
			startTime: $('#startTime').val(),
			endTime: $('#endTime').val(),
			endDate: $('#endDate').val(),
			doesRepeat: $('#doesRepeat').is(':checked') ? 1 : 0,
			repeatType: $('#repeatType').val(),
			repeatsEvery: $('#repeatsEvery').val(),
			until: $('#until').val(),
			weekdaySunday: $('#weekdaySunday').is(':checked') ? 1 : 0,
			weekdayMonday: $('#weekdayMonday').is(':checked') ? 1 : 0,
			weekdayTuesday: $('#weekdayTuesday').is(':checked') ? 1 : 0,
			weekdayWednesday: $('#weekdayWednesday').is(':checked') ? 1 : 0,
			weekdayThursday: $('#weekdayThursday').is(':checked') ? 1 : 0,
			weekdayFriday: $('#weekdayFriday').is(':checked') ? 1 : 0,
			weekdaySaturday: $('#weekdaySaturday').is(':checked') ? 1 : 0,
			repeatBy: $('#doesRepeatRow_monthDetailsRow input:radio:checked').val(),
			endsOn: $('#doesRepeatRow_endsOnRow input:radio:checked').val(),
			hasExceptions: $('#hasExceptions').is(':checked') ? 1 : 0,
			exceptions: eventExceptions.toString()
			};

		$.post('schedule.php', params, function (data)
		{
			if (data.Response == 'OK')
			{
				$('#calendar').fullCalendar('refetchEvents');
			}
			else
			{
				alert(data.ResponseVerbose);
				return;
			}

			$('#dialogCreateModify').dialog('close');
		}, 'json');
	}

	////////////////////////////////////////////////////////////////////////////
	function dialogCreateModify_modify()
	{
		if ($('#eventID').val() == '' || $('#eventID').val() == undefined) { alert('Please specify an event.'); return; }

		var params = {
			cmd: 'ajaxModifyEvent',
			execute: 1,
			breakInstance: $('#shouldBreakInstance').val(), 
			instanceID: $('#instanceID').val(),
			eventID: $('#eventID').val(),
			eventType: $('#eventType').val(),
			startDate: $('#startDate').val(),
			startTime: $('#startTime').val(),
			endTime: $('#endTime').val(),
			endDate: $('#endDate').val(),
			doesRepeat: $('#doesRepeat').is(':checked') ? 1 : 0,
			repeatType: $('#repeatType').val(),
			repeatsEvery: $('#repeatsEvery').val(),
			until: $('#until').val(),
			weekdaySunday: $('#weekdaySunday').is(':checked') ? 1 : 0,
			weekdayMonday: $('#weekdayMonday').is(':checked') ? 1 : 0,
			weekdayTuesday: $('#weekdayTuesday').is(':checked') ? 1 : 0,
			weekdayWednesday: $('#weekdayWednesday').is(':checked') ? 1 : 0,
			weekdayThursday: $('#weekdayThursday').is(':checked') ? 1 : 0,
			weekdayFriday: $('#weekdayFriday').is(':checked') ? 1 : 0,
			weekdaySaturday: $('#weekdaySaturday').is(':checked') ? 1 : 0,
			repeatBy: $('#doesRepeatRow_monthDetailsRow input:radio:checked').val(),
			endsOn: $('#doesRepeatRow_endsOnRow input:radio:checked').val(),
			hasExceptions: $('#hasExceptions').is(':checked') ? 1 : 0,
			exceptions: eventExceptions.toString()
			};

		$.post('schedule.php', params, function (data)
		{
			if (data.Response == 'OK')
			{
				$('#calendar').fullCalendar('refetchEvents');
			}
			else
			{
				alert(data.ResponseVerbose);
			}

			$('#dialogCreateModify').dialog('close');
		}, 'json');
	}

	////////////////////////////////////////////////////////////////////////////
	function dialogCreateModify_remove(event)
	{
		// Note: Do NOT implement the feature of asking the user whether 
		//  he or she would like to remove the instance or the series. This 
		//  is because firstly, the user chooses whether he or she is going 
		//  to modify the instance or the series. AFTER they make this choice 
		//  he or she is presented with the dialog that contains the remove 
		//  button (this function). Therefore they have already made the 
		//  decision on what they would be removing. To ask this again 
		//  (although helpful, perhaps) would open a can of evil and is 
		//  redundant.

		var buttons = {
			'No': function() { var func = $(this).dialog('option', 'func'); func(2); $(this).dialog('close'); },
			'Yes': function() { var func = $(this).dialog('option', 'func'); func(1); $(this).dialog('close'); }
			};

		if (event.IsInstance)
			var msg = 'Are you sure you would like to remove this instance?';
		else
			var msg = 'Are you sure you would like to remove all instances in this recurrence?';
		
		confirmDialog('Remove ' + (event.IsInstance ? '' : 'Recurring ') + 'Event', msg, buttons, function(ret)
		{
			breakInstance = (ret == 1) ? 1 : 0;
	
			if (ret != 1)
			{
				$('#calendar').fullCalendar('refetchEvents');
				return;
			}

			$('#dialogCreateModify').dialog('close');

			$.post('schedule.php', { cmd: 'ajaxRemoveEvent', execute: 1, instanceID: $('#instanceID').val() }, function (data)
			{
				if (data.Response == 'OK')
					$('#calendar').fullCalendar('refetchEvents');
				else
					alert(data.ResponseVerbose);
			}, 'json');
		});
	}

	////////////////////////////////////////////////////////////////////////////
	function showEvents(id)
	{
		id = typeof(id) != 'undefined' ? id : '';

		var html = '<span class="fieldLabel"></span>&nbsp;';
		html += '<select id="eventID">';
		html += '<option value="">Please select an event...</option>';

		$.post('schedule.php', {cmd: 'ajaxGetEventSelectOptions', execute: 1, eventType: $('#eventType').val(), id: id}, function (data, textStatus)
		{
			html += data;
			html += '</select>';
			$('#events').html(html);
		});
	}

	////////////////////////////////////////////////////////////////////////////
	function toggleRepeat()
	{
		if ($('#doesRepeat').is(':checked'))
		{
			$('#doesRepeatRow').removeClass('disabledText');
			$('#doesRepeatRow :input').attr('disabled','');
			updateRepeatsInterface();
			verifyEndDate();

			$('#hasExceptionsRow legend').removeClass('disabledText');
			$('#hasExceptionsRow legend :input').attr('disabled','');

			if ($('#hasExceptions').is(':checked'))
			{
				$('#hasExceptionsRow *').removeClass('disabledText');
				$('#hasExceptionsRow *').attr('disabled','');
			}
		}
		else
		{
			$('#doesRepeatRow').addClass('disabledText');
			$('#doesRepeatRow :input').attr('disabled','disabled');

			$('#hasExceptionsRow *').addClass('disabledText');
			$('#hasExceptionsRow *').attr('disabled','disabled');
		}

		$('#doesRepeat').attr('disabled','');
	}

	////////////////////////////////////////////////////////////////////////////
	function updateRepeatsInterface()
	{
		$('#doesRepeatRow_weekDetailsRow').hide();
		$('#doesRepeatRow_monthDetailsRow').hide();

		switch($('#repeatType').val())
		{
			case 'daily': $('#repeatsEveryCaption').html(' days'); break;
			case 'weekly': $('#repeatsEveryCaption').html(' weeks'); $('#doesRepeatRow_weekDetailsRow').show(); break;
			case 'monthly': $('#repeatsEveryCaption').html(' months'); $('#doesRepeatRow_monthDetailsRow').show(); break;
			case 'yearly': $('#repeatsEveryCaption').html(' years'); break;
			default: $('#repeatsEveryCaption').html('');
		}

		switch($('#doesRepeatRow_endsOnRow input:radio:checked').val())
		{
			case 'never': $('#until').attr('disabled','disabled'); break;
			case 'until': $('#until').attr('disabled',''); break;
		}
	}

	////////////////////////////////////////////////////////////////////////////
	// This function is called when text changes in the start date field. It
	//  sets the end date such that you cannot select a start date after an end
	//  date or vice versa...
	function verifyEndDate()
	{
		var startDate = $('#startDate').datepicker('getDate');
		$('#endDate').datepicker('option', 'minDate', startDate);
		$('#until').datepicker('option', 'minDate', startDate);
		$('#startDate').blur();
	}

	////////////////////////////////////////////////////////////////////////////
	// This function is called when (in Repeat) a start or end date changes. It
	//  sends the bound of when you can set an exception...
	function verifyExceptionDate()
	{
	}

	////////////////////////////////////////////////////////////////////////////
	// Checks to make sure the user doesn't create an exception for the start
	//  date or end date (returns true if value, otherwise false)...
	function validateExceptionDate()
	{
	}

	////////////////////////////////////////////////////////////////////////////
	function toggleException()
	{
		if ($('#hasExceptions').is(':checked'))
		{
			$('#hasExceptionsRow *').removeClass('disabledText');
			$('#hasExceptionsRow :input').attr('disabled','');
			updateRepeatsInterface();
		}
		else
		{
			$('#hasExceptionsRow *').addClass('disabledText');
			$('#hasExceptionsRow :input').attr('disabled','disabled');
		}

		$('#hasExceptions').attr('disabled','');
	}

	/* ]]> */
	</script>

	<style type="text/css">

		.fieldLabel { display: inline-block; width: 100px; }
		#startDate { width: 80px; }
		#startTime { width: 80px; }
		#endTime { width: 80px; }
		#endDate { width: 80px; }
		#eventType, #eventID { width: 210px; }
		#whenRow, #doesRepeatRow, #eventID { margin-bottom: 8px; }

		.songTip { margin: 0px; padding: 0px; font-size: 12px; }
		.songTip .songTipTitle { display: block; text-align: center; font-size: 13px; font-weight: bold; margin-bottom: 8px; }
		.songTip .songTipRow { display: table-row; }
		.songTip .songTipField { display: table-cell; width: 80px; font-weight: bold; }
		.songTip .songTipDescription { display: table-cell; padding-bottom: 4px; }

		#calendarLoading { margin-left: 20px; color: #3db9cf; font-size: 14px; }

	</style>

<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();                                   # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>


<div id="slotMinutes">
	<span id="slotMinutesField">Slot Resolution:</span>
	<span id="slotMinutesSliderValue">30 mins</span>
	<div id="slotMinutesSlider"></div>
</div>


<h4>Schedule<span id="calendarLoading">Loading, Please Wait...</span></h4>


<div id="calendar" class="fullCalendar"></div>


<div id="dialogCreateModify" class="dialog">

	<form>
	<input type="hidden" id="instanceID" name="instanceID" value="" />
	<input type="hidden" id="shouldBreakInstance" name="shouldBreakInstance" value="" />




	<div id="eventTypeRow">
		<span class="fieldLabel">Event Type:</span>

		<select id="eventType" onchange="showEvents()">
		<option>Please select an event type...</option>
		<option>Alert</option>
		<option>Announcement</option>
		<option>Feature</option>
		<option>PSA</option>
		<option value="ShowMetadata">Show</option>
		<option value="TicketGiveaway">Ticket Giveaway</option>
		<option>Underwriting</option>
		</select>
	</div>




	<div id="events"></div>




	<div id="whenRow">
		<span class="fieldLabel">When:</span>

		<?php
		$initalStartDate = $initalStartTime = $initalEndTime = $initalEndDate = '';
		$hasStartInfo = $uri->hasKey('startDate') && $uri->hasKey('startTime');
		if ($hasStartInfo)
		{
			$startTime = strtotime($uri->getKey('startDate') . ' ' . $uri->getKey('startTime'));
			$initalStartDate = date('n/j/Y', $startTime);
			$initalStartTime = date('g:ia', $startTime);
			$initalEndTime = date('g:ia', strtotime('+1 hour', $startTime));
			$initalEndDate = date('n/j/Y', strtotime('+1 hour', $startTime));
		}
		else
		{
			$initalStartDate = date('n/j/Y');
			$initalStartTime = date('g:ia');
			$initalEndTime = date('g:ia', strtotime('+1 hour'));
			$initalEndDate = date('n/j/Y', strtotime('+1 hour'));
		}
		?>
		<input type="text" id="startDate" readonly="readonly" value="<?php echo $initalStartDate; ?>" onchange="verifyEndDate(); verifyExceptionDate();" />
		<input type="text" id="startTime" value="<?php echo $initalStartTime; ?>" /> to
		<input type="text" id="endTime" value="<?php echo $initalEndTime; ?>" />
		<input type="text" id="endDate" readonly="readonly" value="<?php echo $initalEndDate; ?>" />
	</div>




	<fieldset id="doesRepeatRow">
	<legend><input type="checkbox" id="doesRepeat" onchange="toggleRepeat();" /> Repeats...</legend>

		<div id="doesRepeatRow_repeatsRow">
			<span class="fieldLabel">Repeats:</span>
			<select id="repeatType" onchange="updateRepeatsInterface();">
			<option value="daily">Daily</option>
			<option value="weekly">Weekly</option>
			<option value="monthly">Monthly</option>
			<option value="yearly">Yearly</option>
			</select>
		</div>

		<div id="doesRepeatRow_repeatEveryRow">
			<span class="fieldLabel">Repeat every:</span>
			<select id="repeatsEvery">
			<?php for ($i = 1; $i < 31; $i++) echo "<option>$i</option>"; ?>
			</select><span id="repeatsEveryCaption"> days</span>
		</div>

		<div id="doesRepeatRow_weekDetailsRow">
			<span class="fieldLabel">Repeat on:</span>
			<input type="checkbox" id="weekdaySunday" /> S&nbsp;&nbsp;&nbsp;
			<input type="checkbox" id="weekdayMonday" /> M&nbsp;&nbsp;&nbsp;
			<input type="checkbox" id="weekdayTuesday" /> T&nbsp;&nbsp;&nbsp;
			<input type="checkbox" id="weekdayWednesday" /> W&nbsp;&nbsp;&nbsp;
			<input type="checkbox" id="weekdayThursday" /> T&nbsp;&nbsp;&nbsp;
			<input type="checkbox" id="weekdayFriday" /> F&nbsp;&nbsp;&nbsp;
			<input type="checkbox" id="weekdaySaturday" /> S
		</div>

		<div id="doesRepeatRow_monthDetailsRow">
			<span class="fieldLabel">Repeat by:</span>
			<input type="radio" name="repeatBy" value="dayOfMonth" checked="checked" /> day of the month&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="radio" name="repeatBy" value="dayOfWeek" /> day of the week
		</div>

		<div id="doesRepeatRow_endsOnRow">
			<span class="fieldLabel">Ends on:</span>
			<input type="radio" name="endsOn" value="never" checked="checked" onclick="updateRepeatsInterface();" /> Never&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="radio" name="endsOn" value="until" onclick="updateRepeatsInterface();" /> Until
			<input type="text" id="until" readonly="readonly" onchange="verifyExceptionDate();" />
		</div>

	</fieldset>




	<fieldset id="hasExceptionsRow">
	<legend><input type="checkbox" id="hasExceptions" onchange="toggleException();" /> Exceptions...</legend>
		<div id="exceptionContainer"></div>
		<span class="fieldLabel">Exception:</span>
		<input type="text" id="exceptionOn" readonly="readonly" onchange="validateExceptionDate();" />
		<input type="button" id="btnAddException" value="Add" />
	</fieldset>


	
	</form>

</div>



<div id="dialogConfirm" class="dialog">
<p id="dialogConfirm_message"></p>
</div>



<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();                                 # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
