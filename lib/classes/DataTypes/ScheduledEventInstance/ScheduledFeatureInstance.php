<?php
class ScheduledFeatureInstance extends ExecutableScheduledEventInstance
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new FeatureInstanceColumnSet()
		));
		
		$this->DISCRIMINATOR = 'ScheduledFeatureInstance';
		
		parent::__construct($params);
	}
}
?>