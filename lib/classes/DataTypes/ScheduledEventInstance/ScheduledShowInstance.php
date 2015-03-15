<?php
class ScheduledShowInstance extends ScheduledEventInstance
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new ShowInstanceColumnSet()
		));
		
		$this->DISCRIMINATOR = 'ScheduledShowInstance';
		
		parent::__construct($params);
	}
}
?>