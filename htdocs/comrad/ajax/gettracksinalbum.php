<?php
	
	require_once('initialize.php');
	
	if ($uri->hasKey('albumid')) {
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . date('D, d M Y H:i:s') . 'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: text/x-json');
		
		$results = DB::getInstance('MySql')->find(new Track(array('AlbumID' => $uri->getKey('albumid'))), $count, array('sortcolumn' => 'TrackNumber', 'limit' => false));
		
		$objects = array();
		foreach ($results as $result) {
			$result->fetchForeignKeyItem('Album');
			array_push($objects, $result->toArray());
		}
		
		echo json_encode($objects);
		exit();
	}
?>