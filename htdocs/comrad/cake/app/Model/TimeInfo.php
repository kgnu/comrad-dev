<?php
class TimeInfo extends AppModel {
	var $name = 'TimeInfo';
	
	var $useTable = 'TimeInfo';
	var $primaryKey = 'ti_Id';
	// var $displayField = 'e_Title';
	
	var $hasMany = array(
		'ScheduledEvent' => array(
			'className' => 'ScheduledEvent',
			'foreignKey' => 'se_TimeInfoId',
			'dependent' => true
		)
	);
}
?>
