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
		$intervalMultiplier = 128;
		while ($intervalMultiplier >= 1) {
			while (strtotime('+' . $interval * $intervalMultiplier . ' week', $startDateTime) < strtotime('-' . $interval . ' week', $timeWindowStart)) {
				$startDateTime = strtotime('+' . $interval . ' week', $startDateTime);
			}
			$intervalMultiplier = $intervalMultiplier / 2;
		}
		//advance $startDateTime to close to $timeWindowStart to avoid iterations in the sequence below
		while (strtotime('+' . $interval . ' week', $startDateTime) < strtotime('-' . $interval . ' week', $timeWindowStart)) {
			$startDateTime = strtotime('+' . $interval . ' week', $startDateTime);
		}
		
		do {
			for ($i = 0; $i < 7; $i++) {
				
				if ($this->{'WeeklyOn'.date('l', $startDateTime)} && $startDateTime + $this->Duration * 60 > $timeWindowStart && $startDateTime < $timeWindowEnd) {
					$sei = new ScheduledEventInstance(array(
						'ScheduledEventId' => $scheduledEvent->Id,
						'ScheduledEvent' => $scheduledEvent,
						'StartDateTime' => $startDateTime,
						'Duration' => $this->Duration
					));
			
					array_push($results, $sei);
				}
				$startDateTime = strtotime('+1 day', $startDateTime);
			}
			$startDateTime = strtotime('+'.($interval - 1).' week', $startDateTime);
		} while ($startDateTime < $endDateTime);
		
		return $results;
	}
}
?>