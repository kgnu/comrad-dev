<?php
class ExecutableScheduledEventInstance extends ScheduledEventInstance
{
	public function __construct($params = array())
	{
		$this->addColumns(array(
			'Executed' => array(
				'type' => 'Date'
			),
			'Order' => array(
			    'type' => 'Integer',
			)
		));
		
		parent::__construct($params);
	}
}
?>