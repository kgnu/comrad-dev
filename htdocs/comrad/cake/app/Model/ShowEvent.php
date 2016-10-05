<?php
App::import('Model', 'Event');
class ShowEvent extends Event {
	var $name = 'ShowEvent';
	
	var $actsAs = array('Inheritable' => array(
		'inheritanceField' => 'e_DISCRIMINATOR',
		'method' => 'STI',
		'fields' => array(
			'e_Id',
			'e_Title',
			'e_HasHost',
			'e_HostId',
			'e_RecordAudio',
			'e_URL',
			'e_Source',
			'e_Category',
			'e_Class',
			'e_ShortDescription',
			'e_LongDescription',
			'e_Active'
		)
	));
	
	var $belongsTo = array(
		'Host' => array(
			'foreignKey' => 'e_HostId'
		)
	);
}
?>
