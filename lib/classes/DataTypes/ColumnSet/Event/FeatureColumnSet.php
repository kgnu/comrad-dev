<?php
class FeatureColumnSet extends EventColumnSet
{
	public function __construct()
	{
		parent::__construct();
		
		$this->addColumns(array(
			'ProducerName' => array(
				'type' => 'UppercaseString',
				'required' => true,
				'tostring' => 'Producer Name'
			),
			'GuestName' => array(
				'type' => 'UppercaseString',
				'tostring' => 'Guest Name'
			),
			'Description' => array(
				'type' => 'String'
			),
			'InternalNote' => array(
				'type' => 'ShortString',
				'tostring' => 'Internal Note'
			)
		));
	}
}
?>
