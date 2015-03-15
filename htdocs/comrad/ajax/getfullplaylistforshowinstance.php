<?php
	$disableAuthentication = true;
	require_once('initialize.php');
	PermissionManager::getInstance()->disableAuthorization();
	
	if ($uri->hasKey('showid')) {
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . date('D, d M Y H:i:s') . 'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: text/json');
		
		$showId = $uri->getKey('showid');
		
		$results = DB::getInstance('MySql')->find(new ScheduledShowInstance(array('Id' => $showId)));
		$scheduledShowInstance = $results[0];
		
		$results = array();
		
		// Executed Events
		$executedScheduledEventInstances = EventManager::getInstance()->getEventsBetween(
			$scheduledShowInstance->StartDateTime,
			$scheduledShowInstance->StartDateTime + $scheduledShowInstance->Duration * 60,
			array('Announcement', 'Feature', 'PSA', 'Underwriting'),
			false,
			array(array('Executed', '>', 0))
		);
		foreach ($executedScheduledEventInstances as $executedScheduledEventInstance) {
			array_push($results, $executedScheduledEventInstance->toArray());
		}
		
		// Executed Floating Show Elements
		$floatingShowElements = FloatingShowElementManager::getInstance()->getFloatingShowElementsForShow($showId);
		foreach ($floatingShowElements as $floatingShowElement) {
			if ($floatingShowElement->Executed > 0) {
				array_push($results, $floatingShowElement->toArray());
			}
		}
		
		$json = json_encode($results);
		
		echo ($uri->hasKey('callback') ? $uri->getKey('callback') . '(' . $json .')' : $json);
		
		exit();
	}
?>