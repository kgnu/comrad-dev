<?php
class PSAEvent extends EventWithCopy
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new PSAColumnSet()
		));
		
		$this->DISCRIMINATOR = 'PSAEvent';
		
		parent::__construct($params);
	}
}
?>