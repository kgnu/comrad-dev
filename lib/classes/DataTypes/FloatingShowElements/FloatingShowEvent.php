<?php
class FloatingShowEvent extends FloatingShowElement
{
	public function __construct($params = array())
	{
		$this->DISCRIMINATOR = 'FloatingShowEvent';
		
		$this->addColumns(array(
			'EventId' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'Event',
				'required' => true
			),
			'Event' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'Event',
				'localcolumn' => 'EventId',
				'foreigncolumn' => 'Id'
			)
		));
		
		parent::__construct($params);
	}
	
	public function __toString()
	{
		return 'FloatingShowEvent #'.$this->Id;
	}
}
?>