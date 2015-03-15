<?php
class AlertEvent extends Event
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new AlertColumnSet()
		));
		
		$this->DISCRIMINATOR = 'AlertEvent';
		
		parent::__construct($params);
	}
}
?>