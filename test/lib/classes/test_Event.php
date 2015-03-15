#!/usr/bin/php
<?php
	require_once('../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();

	echo "UNIT TEST: Event\n";


	$event = new Event(231);
	$event_exists = $event->populate();
	echo $event_exists ? 'yes' : 'no';
	echo "\n";

	echo 'Name: ' . $event->getName() . "\n";
	echo 'Description: ' . $event->getDescription() . "\n";

	if (!$event_exists)
	{
		$event->setName(date('r'));
		$event->setDescription('My Desc: ' . date('r'));
		echo 'update 1...' . "\n";
		$event->update(); echo $event->getID() . "\n";
		echo 'update 2...' . "\n";
		$event->update(); echo $event->getID() . "\n";
		echo 'update 3...' . "\n";
		$event->update(); echo $event->getID() . "\n";
		echo 'asdf';
	}

	echo "Done\n";
?>
