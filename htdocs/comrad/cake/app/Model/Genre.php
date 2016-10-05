<?php
class Genre extends AppModel {
	var $name = 'Genre';
	
	var $useTable = 'Genres';
	var $primaryKey = 'g_GenreID';
	var $displayField = 'g_Name';
	
	var $hasMany = array(
		'Album' => array(
			'className' => 'Album',
			'foreignKey' => 'a_GenreID',
			'order' => 'Album.a_Title ASC'
		)
	);
}
?>
