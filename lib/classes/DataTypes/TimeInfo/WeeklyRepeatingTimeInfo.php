<?php
class WeeklyRepeatingTimeInfo extends RepeatingTimeInfo
{
	public function __construct($params = array())
	{
		$this->addColumns(array(
			'WeeklyOnSunday' => array(
				'type' => 'Boolean',
				'required' => true
			),
			'WeeklyOnMonday' => array(
				'type' => 'Boolean',
				'required' => true
			),
			'WeeklyOnTuesday' => array(
				'type' => 'Boolean',
				'required' => true
			),
			'WeeklyOnWednesday' => array(
				'type' => 'Boolean',
				'required' => true
			),
			'WeeklyOnThursday' => array(
				'type' => 'Boolean'
			),
			'WeeklyOnFriday' => array(
				'type' => 'Boolean',
				'required' => true
			),
			'WeeklyOnSaturday' => array(
				'type' => 'Boolean',
				'required' => true
			)
		));
		
		$this->DISCRIMINATOR = 'WeeklyRepeatingTimeInfo';
		
		parent::__construct($params);
	}
	
	public function createScheduledEventInstancesForTimeWindow($timeWindowStart, $timeWindowEnd, $scheduledEvent) {
		$results = array();
		$endDateTime = ($this->EndDate && $this->EndDate > 0 ? $this->EndDate + $this->Duration * 60 + 86400 : $timeWindowEnd);
		$startDateTime = $this->StartDateTime;
		$interval = ($this->Interval > 0 ? $this->Interval : 1);
		
		$originalSDT = $startDateTime;
		$intervalMultiplier = 1024;
		while ($intervalMultiplier >= 1) {
			while (strtotime('+' . $interval * $intervalMultiplier . ' week', $startDateTime) < strtotime('-' . $interval . ' week', $timeWindowStart)) {
				$startDateTime = strtotime('+' . $interval * $intervalMultiplier . ' week', $startDateTime);
			}
			$intervalMultiplier = $intervalMultiplier / 2;
		}
		//advance $startDateTime to close to $timeWindowStart to avoid iterations in the sequence below
		while (strtotime('+' . $interval . ' week', $startDateTime) < strtotime('-' . $interval . ' week', $timeWindowStart)) {
			$startDateTime = strtotime('+' . $interval . ' week', $startDateTime);
		}
		
		//loop through days in the requested time period, but only check days where the event is taking place
		//we'll put together an array that will let us know how many days to skip at a time
		//ie, if the event is on Tuesday and Saturday and we're starting on a Monday, the initial change would be +1 day and after that the pattern would be +4 days, +3 days
		$currentDayOfWeek = date('N', $startDateTime); //1-7 for day of the week, 1 is Monday
		$daysOfWeekWithEvent = $this->getArrayOfDaysWithEvent();
		
		//if no days of the week have an event, just return the current result set
		if (count($daysOfWeekWithEvent) == 0) 
			return $results;
		
		$nextDay = $currentDayOfWeek;
		$addForFirstDay = 0;
		if (in_array($currentDayOfWeek, $daysOfWeekWithEvent)) {
			// the event is today
		} else {
			//find the next day
			do {
				$addForFirstDay++;
				$nextDay++;
				if ($nextDay > 7) 
					$nextDay = 1;
			} while (!in_array($nextDay, $daysOfWeekWithEvent));
		}
	
		
		$startDateTime = strtotime('+' . $addForFirstDay . ' day', $startDateTime);
		if ($startDateTime + $this->Duration * 60 > $timeWindowStart && $startDateTime < $timeWindowEnd) {
			$sei = new ScheduledEventInstance(array(
				'ScheduledEventId' => $scheduledEvent->Id,
				'ScheduledEvent' => $scheduledEvent,
				'StartDateTime' => $startDateTime,
				'Duration' => $this->Duration
			));
	
			array_push($results, $sei);
		}
	
		$subsequentDaysToAdd = array();
		
		$j = 0;
		for ($i = 1; $i <= 7; $i++) {
			$nextDay++;
			$j++;
			if ($nextDay > 7) 
				$nextDay = 1;
			if (in_array($nextDay, $daysOfWeekWithEvent)) {
				$subsequentDaysToAdd[] = $j;
				$j = 0;
			}
		}
		
		
		
		do {
			
			for ($i = 0; $i <= count($subsequentDaysToAdd) - 2; $i++) {
				$startDateTime = strtotime('+' . $subsequentDaysToAdd[$i] . ' day', $startDateTime);
				if ($startDateTime + $this->Duration * 60 > $timeWindowStart && $startDateTime < $timeWindowEnd) {
					$sei = new ScheduledEventInstance(array(
						'ScheduledEventId' => $scheduledEvent->Id,
						'ScheduledEvent' => $scheduledEvent,
						'StartDateTime' => $startDateTime,
						'Duration' => $this->Duration
					));
			
					array_push($results, $sei);
				}
			}
			
			//skip a week for intervals
			$startDateTime = strtotime('+'.($interval - 1).' week', $startDateTime);
		
			$startDateTime = strtotime('+' . $subsequentDaysToAdd[count($subsequentDaysToAdd) - 1] . ' day', $startDateTime);
			if ($startDateTime + $this->Duration * 60 > $timeWindowStart && $startDateTime < $timeWindowEnd) {
				$sei = new ScheduledEventInstance(array(
					'ScheduledEventId' => $scheduledEvent->Id,
					'ScheduledEvent' => $scheduledEvent,
					'StartDateTime' => $startDateTime,
					'Duration' => $this->Duration
				));
		
				array_push($results, $sei);
			}
		} while ($startDateTime < $endDateTime);
		
		return $results;
	}
	
	//gets an array of the days of week that have an event, where 1 is Monday and 7 is Sunday7. Lines up with date('N') formatting in PHP
	private function getArrayOfDaysWithEvent() {
		$arrayOfDaysWithEvent = array();
		if ($this->WeeklyOnMonday)
			$arrayOfDaysWithEvent[] = 1;
		if ($this->WeeklyOnTuesday)
			$arrayOfDaysWithEvent[] = 2;
		if ($this->WeeklyOnWednesday)
			$arrayOfDaysWithEvent[] = 3;
		if ($this->WeeklyOnThursday)
			$arrayOfDaysWithEvent[] = 4;
		if ($this->WeeklyOnFriday)
			$arrayOfDaysWithEvent[] = 5;
		if ($this->WeeklyOnSaturday)
			$arrayOfDaysWithEvent[] = 6;
		if ($this->WeeklyOnSunday)
			$arrayOfDaysWithEvent[] = 7;
		return $arrayOfDaysWithEvent;
	}
}
?>