<?php
class MonthlyRepeatingTimeInfo extends RepeatingTimeInfo
{
	public function __construct($params = array())
	{
		$this->addColumns(array(
			'MonthlyRepeatBy' => array(
				'type' => 'Enumeration',
				'possiblevalues' => array(
					'DAY_OF_MONTH',
					'DAY_OF_WEEK'
				)
			)
		));
		
		$this->DISCRIMINATOR = 'MonthlyRepeatingTimeInfo';
		
		parent::__construct($params);
	}
}
?>