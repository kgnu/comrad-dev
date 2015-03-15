<?php
class ShowEvent extends Event
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new ShowColumnSet()
		));
		
		$this->DISCRIMINATOR = 'ShowEvent';
		
		parent::__construct($params);
	}
}
?>