<?php
class UnderwritingEvent extends EventWithCopy
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new UnderwritingColumnSet()
		));
		
		$this->DISCRIMINATOR = 'UnderwritingEvent';
		
		parent::__construct($params);
	}
}
?>