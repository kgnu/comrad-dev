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
	
	if ($_GET['noCallers']) exit();
	
	//send an email to the winner
	$headers  = 'MIME-Version: 1.0' . "\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
	
	// Additional headers
	$headers .= 'To: ' . $_GET['winnerName'] . ' <' . $_GET['winnerEmail'] . '>' . "\n";
	$headers .= 'From: KGNU Tickets <tickets@kgnu.org>' . "\n";
	switch ($event->TicketType) {
		case 'Paper Ticket':
			$emailBody = file_get_contents(str_replace('htdocs/comrad/ajax', 'lib/resources/WinnerPaperTicketEmail.html', dirname(__FILE__)));
			break;
		case 'Guest List Ticket':
			$emailBody = file_get_contents(str_replace('htdocs/comrad/ajax', 'lib/resources/WinnerGuestListTicketEmail.html', dirname(__FILE__)));
			break;
		case 'Other Giveaway':
		default:
			$emailBody = file_get_contents(str_replace('htdocs/comrad/ajax', 'lib/resources/WinnerOtherGiveawayEmail.html', dirname(__FILE__)));
			break;
	}
	//replace tokens in the email
	$emailBody = str_replace('[WinnerName]', $_GET['winnerName'], $emailBody);
	$emailBody = str_replace('[WinnerAddress]', $_GET['winnerAddress'], $emailBody);
	$emailBody = str_replace('[ShowName]', $event->ShowName, $emailBody);
	$emailBody = str_replace('[ShowDate]', date('n/j/y', $event->ShowDate), $emailBody);
	$emailBody = str_replace('[Venue]', $event->Venue, $emailBody);
	
	mail('tickets@kgnu.org', 'Congratulations! You Won a KGNU Ticket Giveaway', $emailBody, $headers);
	
?>