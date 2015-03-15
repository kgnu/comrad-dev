<?php
class ScheduledLegalIdInstance extends ExecutableScheduledEventInstance
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new LegalIdInstanceColumnSet()
		));
		
		$this->DISCRIMINATOR = 'ScheduledLegalIdInstance';
		
		parent::__construct($params);
	}
}
?>