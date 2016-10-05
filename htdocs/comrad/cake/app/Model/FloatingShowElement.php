<?php
class FloatingShowElement extends AppModel {
	var $name = 'FloatingShowElement';
	
	var $useTable = 'FloatingShowElement';
	var $primaryKey = 'fse_Id';
	var $displayField = 'fse_DISCRIMINATOR';
	
	var $belongsTo = array(
		'Track' => array(
			'foreignKey' => 'fse_TrackId'
		)
	);
}
?>
