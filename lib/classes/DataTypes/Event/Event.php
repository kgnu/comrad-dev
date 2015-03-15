<?php
class Event extends AbstractDBObject
{
	public function __construct($params = array())
	{
		$this->useDiscriminator(array(
			'AlertEvent',
			'AnnouncementEvent',
			'EASTestEvent',
			'FeatureEvent',
			'LegalIdEvent',
			'PSAEvent',
			'ShowEvent',
			'TicketGiveawayEvent',
			'UnderwritingEvent'
		));
		
		$this->addColumnSets(array(
			new EventColumnSet()
		));
		
		parent::__construct($params);
	}
	 
	public function getTableName()
	{
		return 'Event';
	}
	
	public function getTableColumnPrefix() {
		return 'e_';
	}
	 
	public function __toString()
	{
		return 'Event #' . $this->Id;
	}
}
?>