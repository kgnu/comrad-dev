<?php
App::import('Model', 'Event');
class EASTestEvent extends Event {
	var $name = 'EASTestEvent';
	
	var $actsAs = array('Inheritable' => array(
		'inheritanceField' => 'e_DISCRIMINATOR',
		'method' => 'STI',
		'fields' => array(
			'e_Id',
			'e_Title',
			'e_Active'
		)
	));
}
?>
