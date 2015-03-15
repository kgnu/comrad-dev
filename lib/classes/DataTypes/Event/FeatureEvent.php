<?php
class FeatureEvent extends Event
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new FeatureColumnSet()
		));
		
		$this->DISCRIMINATOR = 'FeatureEvent';
		
		parent::__construct($params);
	}
}
?>