<?php
class NonRepeatingTimeInfo extends TimeInfo
{
	public function __construct($params = array())
	{
		$this->DISCRIMINATOR = 'NonRepeatingTimeInfo';
		
		parent::__construct($params);
	}
	
	public function createScheduledEventInstancesForTimeWindow($timeWindowStart, $timeWindowEnd, $scheduledEvent) {
		$sei = new ScheduledEventInstance(array(
			'ScheduledEventId' => $scheduledEvent->Id,
			'ScheduledEvent' => $scheduledEvent,
			'StartDateTime' => $this->StartDateTime,
			'Duration' => $this->Duration
		));
		
		return array($sei);
	}
}
?>