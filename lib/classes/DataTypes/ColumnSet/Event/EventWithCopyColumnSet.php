<?php
class EventWithCopyColumnSet extends EventColumnSet
{
	public function __construct()
	{
		parent::__construct();
		
		$this->addColumns(array(
			'Copy' => array(
				'type' => 'String',
				'required' => true
			)
		));
	}
}
?>
