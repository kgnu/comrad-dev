<?php
class RepeatingTimeInfo extends TimeInfo
{
	public function __construct($params = array())
	{
		$this->addColumns(array(
			'EndDate' => array(
				'type' => 'Date'
			),
			'Interval' => array(
				'type' => 'Integer',
				'required' => true
			)
		));
		
		parent::__construct($params);
	}
}
?>