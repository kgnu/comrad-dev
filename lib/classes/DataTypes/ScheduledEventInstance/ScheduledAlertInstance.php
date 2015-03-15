<?php
class ScheduledAlertInstance extends ExecutableScheduledEventInstance
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new AlertInstanceColumnSet()
		));
		
		$this->DISCRIMINATOR = 'ScheduledAlertInstance';
		
		parent::__construct($params);
	}
}
?>