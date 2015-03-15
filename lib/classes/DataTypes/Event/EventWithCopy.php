<?php
class EventWithCopy extends Event
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new EventWithCopyColumnSet()
		));
		
		parent::__construct($params);
	}
}
?>