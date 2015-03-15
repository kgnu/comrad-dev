<?php
class TimeInfo extends AbstractDBObject
{
	public function __construct($params = array())
	{
		$this->useDiscriminator(array(
			'NonRepeatingTimeInfo',
			'DailyRepeatingTimeInfo',
			'WeeklyRepeatingTimeInfo',
			'MonthlyRepeatingTimeInfo',
			'YearlyRepeatingTimeInfo'
		));
		
		$this->addColumns(array(
			'Id' => array(
				'type' => 'PrimaryKey'
			),
			'StartDateTime' => array(
				'type' => 'Date',
				'required' => true
			),
			'Duration' => array( // In minutes
				'type' => 'Integer',
				'required' => true
			)
		));
		
		parent::__construct($params);
	}
	
	// Override in subclasses
	public function createScheduledEventInstancesForTimeWindow($timeWindowStart, $timeWindowEnd, $scheduledEvent) {
		return array();
	}
	 
	public function getTableName()
	{
		return 'TimeInfo';
	}
	
	public function getTableColumnPrefix() {
		return 'ti_';
	}
	 
	public function __toString()
	{
		return 'TimeInfo #' . $this->Id;
	}
}
?>