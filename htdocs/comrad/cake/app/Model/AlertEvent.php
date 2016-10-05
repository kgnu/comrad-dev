<?php
App::import('Model', 'Event');
class AlertEvent extends Event {
	var $name = 'AlertEvent';
	
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
