<?php
class ScheduledEvent extends AppModel {
	var $name = 'ScheduledEvent';
	
	var $useTable = 'ScheduledEvent';
	var $primaryKey = 'se_Id';
	// var $displayField = 'e_Title';
	
	var $belongsTo = array(
		'Event' => array(
			'foreignKey' => 'se_EventId'
		),
		'TimeInfo' => array(
			'foreignKey' => 'se_TimeInfoId'
		)
	);
	
	var $hasMany = array(
		'ScheduledEventInstance' => array(
			'className' => 'ScheduledEventInstance',
			'foreignKey' => 'sei_ScheduledEventId',
			'order' => 'sei_StartDateTime',
			'dependent' => true
		)
	);
}
?>
