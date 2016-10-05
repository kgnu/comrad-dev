<?php
App::import('Model', 'Event');
class LegalIDEvent extends Event {
	var $name = 'LegalIDEvent';
	
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
