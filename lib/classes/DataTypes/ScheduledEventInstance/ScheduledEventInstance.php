<?php
class ScheduledEventInstance extends AbstractDBObject
{
	public function __construct($params = array())
	{
		$this->useDiscriminator(array(
			'ScheduledAlertInstance',
			'ScheduledAnnouncementInstance',
			'ScheduledEASTestInstance',
			'ScheduledFeatureInstance',
			'ScheduledLegalIdInstance',
			'ScheduledPSAInstance',
			'ScheduledShowInstance',
			'ScheduledTicketGiveawayInstance',
			'ScheduledUnderwritingInstance'
		));
		
		$this->addColumns(array(
			'Id' => array(
				'type' => 'PrimaryKey'
			),
			'ScheduledEventId' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'ScheduledEvent',
				'required' => true
			),
			'ScheduledEvent' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'ScheduledEvent',
				'localcolumn' => 'ScheduledEventId',
				'foreigncolumn' => 'Id'
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
	
	public function getTableColumnPrefix() {
		return 'sei_';
	}
	 
	public function getTableName()
	{
		return 'ScheduledEventInstance';
	}
	 
	public function __toString()
	{
		return 'ScheduledEventInstance #'.$this->Id;
	}
	
	public function toFullCalendarArray() {
		// http://arshaw.com/fullcalendar/docs/event_data/Event_Object/
		$classes = array(get_class($this->ScheduledEvent->Event));
		if (get_class($this) == 'ScheduledEventInstance') array_push($classes, 'fakeInstance');
		$array = $this->toPartialCalendarArray();
		$array['object'] = $this->toArray();
		return $array;
	}
	
	public function toPartialCalendarArray() {
		// http://arshaw.com/fullcalendar/docs/event_data/Event_Object/
		$classes = array(get_class($this->ScheduledEvent->Event));
		if (get_class($this) == 'ScheduledEventInstance') array_push($classes, 'fakeInstance');
		return array(
			'id' => $this->ScheduledEvent->Id,
			'title' => $this->ScheduledEvent->Event->Title,
			'allDay' => false,
			'start' => $this->StartDateTime,
			'end' => $this->StartDateTime + $this->Duration * 60,
			'className' => $classes,
			'object' => NULL
		);
	}
}
?>