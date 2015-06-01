<?php

	require_once('initialize.php'); 
	
	$results = DB::getInstance('MySql')->find(new ScheduledEvent(array('Id' => $_GET['scheduledEventId'])));
	
	$scheduledEvent = $results[0];
	$scheduledEvent->fetchForeignKeyItem('Event');
	$event = $scheduledEvent->Event;
	
	// Send email to tickets@kgnu.org
	
	$emailBody = '<b>Winner Information:</b><br />';
	if ($_GET['noCallers']) {
		$emailBody .= 'No callers.';
	} else {
		$emailBody .= 'Name: ' . $_GET['winnerName'] . '<br />';
		$emailBody .= 'Phone: ' . $_GET['winnerPhone'] . '<br />';
		$emailBody .= 'Address: ' . $_GET['winnerAddress'] . '<br />';
		$emailBody .= 'Email: ' . $_GET['winnerEmail'] . '<br />';
		$emailBody .= 'Is Listener Member: ' . ($_GET['isListenerMember'] ? 'Yes' : 'No') . '<br />';
	}
	
	$emailBody .= '<br /><br /><b>Giveaway Information:</b><br />';
	$emailBody .= $event->Copy;
	
	//make this an HTML email
	$headers  = 'MIME-Version: 1.0' . "\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
	
	// Additional headers
	$headers .= 'To: KGNU Tickets <tickets@kgnu.org>' . "\n";
	$headers .= 'From: KGNU Tickets <tickets@kgnu.org>' . "\n";

	mail('tickets@kgnu.org', 'Ticket Giveaway: ' . $event->Title, $emailBody, $headers);
	
	//send an email to the purchaser
	switch ($event->TicketType) {
		case 'Paper Ticket':
			
			break;
		case 'Guest List Ticket':
		
			break;
	}
?>