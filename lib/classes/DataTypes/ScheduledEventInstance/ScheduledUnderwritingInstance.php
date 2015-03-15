<?php
class ScheduledUnderwritingInstance extends ExecutableScheduledEventInstance
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new UnderwritingInstanceColumnSet()
		));
		
		$this->DISCRIMINATOR = 'ScheduledUnderwritingInstance';
		
		parent::__construct($params);
	}
}
?>