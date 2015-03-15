<?php
class ScheduledPSAInstance extends ExecutableScheduledEventInstance
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new PSAInstanceColumnSet()
		));
		
		$this->DISCRIMINATOR = 'ScheduledPSAInstance';
		
		parent::__construct($params);
	}
}
?>