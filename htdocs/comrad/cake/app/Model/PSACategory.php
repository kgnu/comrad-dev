<?php
class PSACategory extends AppModel {
	var $name = 'PSACategory';
	
	var $useTable = 'PSACategory';
	var $primaryKey = 'Id';
	var $displayField = 'Title';
	
	var $hasMany = array(
		'PSAEvent' => array(
			'className' => 'PSAEvent',
			'foreignKey' => 'e_PSACategoryId',
			'order' => 'PSAEvent.e_Title ASC',
			'dependent' => true
		)
	);
}
?>
