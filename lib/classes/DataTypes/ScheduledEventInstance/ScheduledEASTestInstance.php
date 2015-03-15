<?php
class ScheduledEASTestInstance extends ExecutableScheduledEventInstance
{
	public function __construct($params = array())
	{
		$this->DISCRIMINATOR = 'ScheduledEASTestInstance';
		
		parent::__construct($params);
	}
}
?>