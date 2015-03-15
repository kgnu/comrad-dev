#!/usr/bin/php
<?php
	require_once('../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();

	echo "UNIT TEST: ScheduledInstanceIterator\n\n";

	$iter = new ScheduledInstanceIterator();
	while ($iter->hasNext())
	{
		$event = $iter->getNext();
		echo $iter->getNextCount() . ' - ' . date('r', $event->getStartDateTime()) . "\n";
	}
	echo "\nDone\n";
?>
