<?php
class Host extends AppModel {
	var $name = 'Host';
	
	var $useTable = 'Host';
	var $primaryKey = 'UID';
	var $displayField = 'Name';
	
	var $hasMany = array(
		'ShowEvent' => array(
			'className' => 'ShowEvent',
			'foreignKey' => 'e_HostId',
			'order' => 'ShowEvent.e_Title ASC'
		)
	);
	
	var $validate = array(
		'Name' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => 'Name is required'
		)
	);
}
?>
