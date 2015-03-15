<?php
class ScheduledAnnouncementInstance extends ExecutableScheduledEventInstance
{
	public function __construct($params = array())
	{
		$this->addColumnSets(array(
			new AnnouncementInstanceColumnSet()
		));
		
		$this->DISCRIMINATOR = 'ScheduledAnnouncementInstance';
		
		parent::__construct($params);
	}
}
?>