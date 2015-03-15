<?php
class FeatureInstanceColumnSet extends AbstractColumnSet
{
	public function __construct()
	{
		parent::__construct();
		
		$this->addColumns(array(
			'GuestName' => array(
				'type' => 'UppercaseString',
				'tostring' => 'Guest Name',
				'showinform' => true
			),
			'Description' => array(
				'type' => 'String',
				'showinform' => true
			),
			'InternalNote' => array(
				'type' => 'ShortString',
				'tostring' => 'Internal Note',
				'showinform' => true
			)
		));
	}
}
?>
