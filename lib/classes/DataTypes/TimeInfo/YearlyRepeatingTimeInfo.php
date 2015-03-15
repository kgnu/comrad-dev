<?php
class YearlyRepeatingTimeInfo extends RepeatingTimeInfo
{
	public function __construct($params = array())
	{
		$this->DISCRIMINATOR = 'YearlyRepeatingTimeInfo';
		
		parent::__construct($params);
	}
}
?>