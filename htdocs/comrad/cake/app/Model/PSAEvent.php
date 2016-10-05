<?php
App::import('Model', 'Event');
class PSAEvent extends Event {
	var $name = 'PSAEvent';
	
	var $actsAs = array('Inheritable' => array(
		'inheritanceField' => 'e_DISCRIMINATOR',
		'method' => 'STI',
		'fields' => array(
			'e_Id',
			'e_Title',
			'e_Copy',
			'e_PSACategoryId',
			'e_StartDate',
			'e_KillDate',
			'e_OrgName',
			'e_ContactName',
			'e_ContactPhone',
			'e_ContactEmail',
			'e_ContactWebsite',
			'e_Active'
		)
	));
	
	var $belongsTo = array(
		'PSACategory' => array(
			'foreignKey' => 'e_PSACategoryId'
		)
	);
}
?>
