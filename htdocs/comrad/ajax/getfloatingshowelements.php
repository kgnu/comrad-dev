<?php
	require_once('initialize.php');
	
	if ($uri->hasKey('showid')) {
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . date('D, d M Y H:i:s') . 'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: text/x-json');
		
		$showId = $uri->getKey('showid');
		
		$results = FloatingShowElementManager::getInstance()->getFloatingShowElementsForShow($showId);
		
		$floatingShowElements = array();
		foreach ($results as $result) {
			array_push($floatingShowElements, $result->toArray());
		}
			
		echo json_encode($floatingShowElements);
		exit();
	}
?>