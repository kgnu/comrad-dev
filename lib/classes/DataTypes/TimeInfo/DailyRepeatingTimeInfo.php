<?php
class DailyRepeatingTimeInfo extends RepeatingTimeInfo
{
	public function __construct($params = array())
	{
		$this->DISCRIMINATOR = 'DailyRepeatingTimeInfo';
		
		parent::__construct($params);
	}
	
	public function createScheduledEventInstancesForTimeWindow($timeWindowStart, $timeWindowEnd, $scheduledEvent) {

		$results = array();
		$endDateTime = ($this->EndDate && $this->EndDate > 0 ? $this->EndDate + $this->Duration * 60 + 86400 : $timeWindowEnd);
		$interval = ($this->Interval > 0 ? $this->Interval : 1);
		$startDateTime = $this->StartDateTime;
		$originalSDT = $startDateTime;
		$intervalMultiplier = 512;
		while ($intervalMultiplier >= 1) {
			while (strtotime('+' . $interval * $intervalMultiplier . ' day', $startDateTime) < strtotime('-' . $interval . ' day', $timeWindowStart)) {
				$startDateTime = strtotime('+' . $interval * $intervalMultiplier . ' day', $startDateTime);
			}
			$intervalMultiplier = $intervalMultiplier / 2;
		}
		for ($startDateTime = $startDateTime; $startDateTime < $endDateTime; $startDateTime = strtotime('+'.$interval.' day', $startDateTime)) {
			
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
		
		return $results;
	}
}
?>