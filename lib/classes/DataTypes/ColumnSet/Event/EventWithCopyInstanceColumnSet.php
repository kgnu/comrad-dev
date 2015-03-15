<?php
class EventWithCopyInstanceColumnSet extends AbstractColumnSet
{
	public function __construct()
	{
		parent::__construct();
		
		$this->addColumns(array(
			'Copy' => array(
				'type' => 'String',
				'showinform' => true
			)
		));
	}
}
?>
