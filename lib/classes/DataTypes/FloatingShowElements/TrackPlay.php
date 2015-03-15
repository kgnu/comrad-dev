<?php
class TrackPlay extends FloatingShowElement
{
	public function __construct($params = array())
	{
		$this->DISCRIMINATOR = 'TrackPlay';
		
		$this->addColumns(array(
			'TrackId' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'Track',
				'required' => true
			),
			'Track' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'Track',
				'localcolumn' => 'TrackId',
				'foreigncolumn' => 'Id'
			)
		));
		
		parent::__construct($params);
	}
	
	public function __toString()
	{
		return 'TrackPlay #'.$this->Id;
	}
}
?>