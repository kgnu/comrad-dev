<?php
class ScheduledEventInstance extends AppModel {
	var $name = 'ScheduledEventInstance';
	
	var $useTable = 'ScheduledEventInstance';
	var $primaryKey = 'sei_Id';
	// var $displayField = 'e_Title';
	
	var $belongsTo = array(
		'ScheduledEvent' => array(
			'foreignKey' => 'sei_ScheduledEventId'
		)
	);
}
?>
