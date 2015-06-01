<?php
class TicketGiveawayEvent extends Event
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new TicketGiveawayColumnSet()
		));
		
		$this->DISCRIMINATOR = 'TicketGiveawayEvent';
		
		parent::__construct($params);
	}
}
?>