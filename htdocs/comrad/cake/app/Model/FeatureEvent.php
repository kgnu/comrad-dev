<?php
App::import('Model', 'Event');
class FeatureEvent extends Event {
	var $name = 'FeatureEvent';
	
	var $actsAs = array('Inheritable' => array(
		'inheritanceField' => 'e_DISCRIMINATOR',
		'method' => 'STI',
		'fields' => array(
			'e_Id',
			'e_ProducerName',
			'e_GuestName',
			'e_Description',
			'e_InternalNote'
			'e_Active'
		)
	));
}
?>
