<?php
class AnnouncementEvent extends EventWithCopy
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new AnnouncementColumnSet()
		));
		
		$this->DISCRIMINATOR = 'AnnouncementEvent';
		
		parent::__construct($params);
	}
}
?>