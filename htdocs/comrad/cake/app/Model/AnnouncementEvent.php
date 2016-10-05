<?php
App::import('Model', 'Event');
class AnnouncementEvent extends Event {
	var $name = 'AnnouncementEvent';
	
	var $actsAs = array('Inheritable' => array(
		'inheritanceField' => 'e_DISCRIMINATOR',
		'method' => 'STI',
		'fields' => array(
			'e_Id',
			'e_Title',
			'e_Copy',
			'e_Active'
		)
	));
}
?>
