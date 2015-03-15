<?php
class ScheduledTicketGiveawayInstance extends ExecutableScheduledEventInstance
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new TicketGiveawayInstanceColumnSet()
		));
		
		$this->DISCRIMINATOR = 'ScheduledTicketGiveawayInstance';
		
		parent::__construct($params);
	}
}
?>