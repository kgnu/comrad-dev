<?php
class LegalIdEvent extends Event
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new LegalIdColumnSet()
		));
		
		$this->DISCRIMINATOR = 'LegalIdEvent';
		
		parent::__construct($params);
	}
}
?>