<?php
class DJComment extends FloatingShowElement
{
	public function __construct($params = array())
	{
		$this->DISCRIMINATOR = 'DJComment';
		
		$this->addColumns(array(
			'Body' => array(
				'type' => 'String',
				'required' => true
			)
		));
		
		parent::__construct($params);
	}
	
	public function __toString()
	{
		return 'DJComment #'.$this->Id;
	}
}
?>