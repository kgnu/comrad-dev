<?php
class Event extends AppModel {
	var $name = 'Event';
	
	var $useTable = 'Event';
	var $primaryKey = 'e_Id';
	var $displayField = 'e_Title';
	
	var $hasMany = array(
		'ScheduledEvent' => array(
			'className' => 'ScheduledEvent',
			'foreignKey' => 'se_EventId',
			'dependent' => true
		)
	);
}
?>
