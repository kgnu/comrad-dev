<?php

################################################################################
# OBJECT:       ExpandRecurringIterator                                        #
# AUTHOR:       Bryan C. Callahan (01/21/2010)                                 #
# COPYRIGHT:    (C) Copyright 2010, Zuriu, LLC. This copyright notice may not  #
#                 be removed. (zuriu.com)  KGNU and revisions of the comrad    #
#                 system have permission to use this class.                    #
# DESCRIPTION:  Iterates through a recurring event.                            #
#                                                                              #
# NOTES:                                                                       #
#  1. Precedence is important!                                                 #
#  2. Exclusions cannot be created for the end date (only the start date)      #
#                                                                              #
# REVISION HISTORY:                                                            #
#   2010/02/20 (BCC) - Rewrote code to access database......... again
#                                                                           [X]#
################################################################################
#                                                                              #
#               --==  IMPLEMENTATION  (DO NOT EDIT BELOW!)  ==--               #
#                                                                              #
################################################################################

final class ExpandRecurringIterator extends AbstractEventsConnector implements InterfaceIterator
{

################################################################################
# PRIVATE MEMBER VARIABLES                                                     #

	private $itemCount;		// Number of items in collection
	private $nextCount;		// Number of times next has been called


	private $event;			// Recurring event we're expanding
	private $eventType;		// We're caching the event type so we don't need to rely on slow reflections for every lookup
	private $eventInstance;		// Scheduled event instance

	private $timeStart;		// Unix time of event start
	private $timeCursor;		// Unix time of current location in iteration
	private $timeEnd;		// Unix time of event end

	private $stepParse;		// ENGLISH string to parse via strtotime (e.g. "+1 month")

	private $limit;			// If infinitly recurring event, limit events by this much

#                                                                           [X]#
################################################################################
# PUBLIC FUNCTIONS                                                             #

	////////////////////////////////////////////////////////////////////////////
	// Default constructor...
	public function __construct($event, &$connection_owner = NULL)
	{
		global $init;

		parent::__construct($connection_owner);

		$this->event = $event;
		$this->eventType = get_class($event);

		// Get the event information (specifically the start tiem information)...
		$this->eventInstance = new ScheduledInstance($event->getParentInstanceID());
		if (!$this->eventInstance->populate()) 
			$init->abort('Inconsistency found: Recurring events must have a validly scheduled parent');

		// Compose the start time and cursor time using the time of the show 
		//  and the date information from the recurring rule... 
//		$this->timeStart = $this->timeCursor = 
//			$this->mergeTimes($this->event->getStartDate(), $this->eventInstance->getStartDateTime());
//
// ^-- I don't think we want the above because it screws with the end points of the iteration --^

		$this->timeStart = $this->timeCursor = $this->event->getStartDate();
		$this->timeEnd = $this->event->getEndDate();

		// Define the step we should parse per iteration...
 		switch ($this->eventType)
		{
			case 'RecurringEventDaily': $this->stepParse = '+1 day'; break;
			case 'RecurringEventWeekly': $this->stepParse = '+1 day'; break;
			case 'RecurringEventMonthly': $this->stepParse = '+1 month'; break;
			case 'RecurringEventYearly': $this->stepParse = '+1 year'; break;
			default: die('Could not count events due to unsupported recurring event type');
		}

		$this->limit = 20;

		$this->itemCount = $this->getEventCount();	// Might not need
		$this->nextCount = 0;
	}

	////////////////////////////////////////////////////////////////////////////
	// Default destruct...
	public function __destruct()
	{
		parent::__destruct();
	}

	////////////////////////////////////////////////////////////////////////////
	// Simply getters and settings...
	public function getLimit() { return $this->limit; }
	public function setLimit($value) { $this->limit = (int) $value; }

	////////////////////////////////////////////////////////////////////////////
	// Returns whether or not there are more results...
	public function hasNext()
	{
		$isBreakingRecurrance = ($this->timeStart < $this->timeEnd);
		$isDone = ($this->timeCursor <= $this->timeEnd);
		$hasReachedLimit = ($this->nextCount+1 > $this->limit);

		if ($isBreakingRecurrance)
			return $isDone;
		else
			return !$hasReachedLimit;
	}

	////////////////////////////////////////////////////////////////////////////
	// Returns the number of items in the collection...
	public function getItemCount()
	{
		// If we do implement this, we could create a race condition :(
		// e.g.
		//
		// If someone adds an exception or changes the recurrances while
		//  we are attempting to iterate through it.
		//
		die('Cannot maintain this without worrying about exceptions.');
		return $this->itemCount;
	}

	////////////////////////////////////////////////////////////////////////////
	// Returns the number of times we've taken a new item from the collection...
	public function getNextCount()
	{
		return $this->nextCount;
	}

	////////////////////////////////////////////////////////////////////////////
	// Gets the next item in the collection...
	public function getNext()
	{
		$ret = $this->timeCursor;
		$this->timeCursor += strtotime($this->stepParse, $this->timeCursor) - $this->timeCursor;
		$this->nextCount++;


		// Calculate how much we should skip in the recurrance with this event (if any)...
		switch ($this->eventType)
		{
			case 'RecurringEventDaily': $skip = ($this->event->getEveryXDays() - 1); break;
			case 'RecurringEventWeekly': $skip = ($this->event->getEveryXWeeks() - 1); break;
			case 'RecurringEventMonthly': $skip = ($this->event->getEveryXMonths() - 1); break;
			case 'RecurringEventYearly': $skip = ($this->event->getEveryXYears() - 1); break;
			default: die('Could not get the next event due to bad recurring event type');
		}

		// Skip time if getEveryX is set greater than one...
		if ($skip > 0) $this->timeCursor += $skip * (strtotime($this->stepParse, $this->timeCursor) - $this->timeCursor);


		// Check to see if there's an exception, if there is, take another whack at it...
		// IMPORTANT NOTE 1! Excpetions cannot be created on the recurring end date!! As 
		//  the user is implicitly daying we want this specific end date (it overrides 
		//  exceptions).
		// IMPORTANT NOTE 2! This is consistent w/ below (for WeeklyRecurring and Monthly 
		//  Recurring Events).
		if ($this->hasException($ret) && $this->hasNext())
		{
			$this->nextCount--;		// Since $ret didn't work, undo
			return $this->getNext();
		}


		// Return the unix timestamp...
		switch ($this->eventType)
		{
			case 'RecurringEventWeekly':

				if (!$this->isValidWeekday($ret) && $this->hasNext())
				{
					$this->nextCount--;		// Since $ret didn't work, undo
					return $this->getNext();
				}
				else
				{
					// IMPORTANT! If we only have a time every MWF and the end 
					//  time is on either STTS then the "invalid" end time will 
					//  be returned!
					//
					// The take away message... MAKE SURE, WHEN YOU SCHEDULE, THAT 
					//  YOUR END DATE IS CORRECT!!!!!!!!!!!!!!
					//
				//	return $ret;
					return $this->mergeWithEventTime($ret);
				}

			case 'RecurringEventMonthly':

				if ($this->event->getRepeatBy() == 'dayOfWeek')
				{
					//return $this->calculateOrder($ret);
					return $this->mergeWithEventTime($this->calculateOrder($ret));
				}
				else
				{
					//return $ret;
					return $this->mergeWithEventTime($ret);
				}

			default:
				//return $ret;
				return $this->mergeWithEventTime($ret);
		}
	}

	////////////////////////////////////////////////////////////////////////////
	// Calculates the start time order of the Week. For example, if the start 
	//  time was Feb 7, 2010, then this function would return a 1 since (because 
	//  the start time is a Sunday) it is the __1st__ Sunday of the month...
	protected function calculateOrderOfWeek()
	{
		$order = 0;

		$startDayMonth = date('F', $this->timeStart);
		$startDay = date('j', $this->timeStart);	// Number of the day (e.g. Jan 4th => 4)
		$startDayYear = date('Y', $this->timeStart);
		$startDayIndex = date('w', $this->timeStart);	// 0 => Sunday, 6 => Saturday

		for ($i = 1; $i <= $startDay; $i++)
		{
			if (date('w', strtotime("$startDayMonth $i, $startDayYear")) == $startDayIndex)
				$order++;
		}

		return $order;
	}

	////////////////////////////////////////////////////////////////////////////
	// Calculates the order timestamp given a month and year. For example, if 
	//  the timeStart was "2nd Monday" (given by calculateOrderOfWeek()) then 
	//  this function would return the "2nd Monday" of the requested month and 
	//  year...
	//
	// Note 1: This function uses a timestamp as an argument to make things 
	//  easier. It is ONLY using this timestamp to derive month and year data.
	//
	// Note 2: Needed because PHP has a bug in strtotime() when trying things 
	//  like strtotime('first monday Feb 2010')
	//
	protected function calculateOrder($timestamp)
	{
		$currentOrder = 0;

		$month = date('F', $timestamp);
		$year = date('Y', $timestamp);

		$nDaysInMonth = date('t', strtotime("$month 1, $year"));
		$startDayIndex = date('w', $this->timeStart);

		$orderOfWeek = $this->calculateOrderOfWeek();

		// Loop through all of the days in the month in order to find the 
		//  order ("2nd Monday", etc)...
		for ($i = 1; $i <= $nDaysInMonth; $i++)
		{
			$timeCurrent = strtotime("$month $i, $year");
			$currentDayIndex = date('w', $timeCurrent);

			// Count this as an order (it's a Monday)...
			if ($currentDayIndex == $startDayIndex) $currentOrder++;

			// If we have counted the desired order (number of Mondays) 
			//  then we hit the correct timestamp...
			if ($currentOrder == $orderOfWeek) return $timeCurrent;
		}

		// We didn't find the requested timeStart order in this month...
		// Note 1: This could happen if we have Jan 31th as start time and 
		//  are digging around in STUPID Feb.
		// Note 2: Typically, this just means that we skip this day in 
		//  iterations.
		return null;
	}

	////////////////////////////////////////////////////////////////////////////
	// Determines whether the given event/timestamp can actually be scheduled on 
	//  this date/time (by checking the exceptions table)...
	protected function hasException($timestamp)
	{
		$timestamp = $this->mergeTimes($timestamp, $this->eventInstance->getStartDateTime());

		$recurringID = $this->event->getID();
		switch ($this->eventType)
		{
			case 'RecurringEventDaily': $recurringType='daily'; break;
			case 'RecurringEventWeekly': $recurringType='weekly'; break;
			case 'RecurringEventMonthly': $recurringType='monthly'; break;
			case 'RecurringEventYearly': $recurringType='yearly'; break;
		}
		$startDateTime = date('Y-m-d H:i:s', $timestamp);

		// -- WE MAY WANT/NEED TO INCLUDE `EndDateTime` ALSO LATER ON --

		$stmt = $this->db->prepare('SELECT * FROM `RecurringEventsException` WHERE `RecurringID`=? AND `RecurringType`=? AND `StartDateTime`=?;');
		if (!$stmt) die('Could not prepare query to determine event exception');
		$stmt->bind_param('iss', $recurringID, $recurringType, $startDateTime);
		$stmt->execute();
		$stmt->store_result();
		$num_rows = $stmt->num_rows;
		$stmt->close();

		return ($num_rows == 1);
	}

	////////////////////////////////////////////////////////////////////////////
	// Usable for Weekly events. Checks a given timestamp to see if it is valid 
	//  for the recurring record. For example, if a MWF record, then Feb 7, 2010 
	//  would be invalid because it is a Saturday (not Mon, Wed, or Fri)...
	protected function isValidWeekday($time)
	{
		$weekdayIndex = date('w', $time);

		return ($this->event->getIsSunday() && $weekdayIndex == 0) || 
			($this->event->getIsMonday() && $weekdayIndex == 1) ||
			($this->event->getIsTuesday() && $weekdayIndex == 2) ||
			($this->event->getIsWednesday() && $weekdayIndex == 3) ||
			($this->event->getIsThursday() && $weekdayIndex == 4) ||
			($this->event->getIsFriday() && $weekdayIndex == 5) ||
			($this->event->getIsSaturday() && $weekdayIndex == 6);
	}

	////////////////////////////////////////////////////////////////////////////
	// Takes the date information from one unix timestamp and time from another 
	//  unix timestamp to create a new "mergeTimes" timestamp...
	protected function mergeTimes($dateInformation, $timeInformation)
	{
		return mktime(
		
			date('H', $timeInformation), 
			date('i', $timeInformation), 
			date('s', $timeInformation), 
			
			date('n', $dateInformation), 
			date('j', $dateInformation), 
			date('Y', $dateInformation)
			
			);
	}

	////////////////////////////////////////////////////////////////////////////
	// Takes the date information from one unix timestamp and merges it with the 
	//  show time...
	protected function mergeWithEventTime($dateInformation)
	{
		return $this->mergeTimes(
			$dateInformation, 
			$this->eventInstance->getStartDateTime()
			);
	}

	////////////////////////////////////////////////////////////////////////////
	// Given the event type and start / end time, calculate number of events...
	//
	// NOTE!!!!!!!!!!!! THIS FUNCTION SHOULD NOT BE USED AS IT DOESN'T RETURN 
	//  THE CORRECT VALUES FOR SPECIFICALLY CONFIGURED MONTHLY AND YEARLY 
	//  RECORDS
	//
	// ALSO, IT DOES NOT FACTOR IN EXCLUSIONS!!
	//
	protected function getEventCount()
	{
		switch ($this->eventType)
		{
			case 'RecurringEventDaily': $step = 60 * 60 * 24; break;					// Days
			case 'RecurringEventWeekly': $step = 7 * 60 * 60 * 24; break;					// Weeks
			case 'RecurringEventMonthly': $step = strtotime('+1 month', $startTime) - $startTime; break;	// Months
			case 'RecurringEventYearly': $step = strtotime('+1 year', $startTime) - $startTime; break;	// Years
			default: die('Could not count events due to bad recurring event type');
		}
		$period = $this->timeEnd - $this->timeStart;
		return floor($period / $step) + 1;
	}

#                                                                           [X]#
################################################################################

}

?>
