#!/usr/bin/php
<?php
	require_once('../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();

	echo "UNIT TEST: RecurringEventIterator\n";


	echo 'Daily...' . "\n";
	$iter = new RecurringEventIterator('Daily');
	while ($iter->hasNext())
	{
		$event = $iter->getNext();
		echo $event->getID() . ' - ' . date('r', $event->getStartDate()) . "\n";
	}
	echo "\n\n";

	echo 'Weekly...' . "\n";
	$iter = new RecurringEventIterator('Weekly');
	while ($iter->hasNext())
	{
		$event = $iter->getNext();
		echo $event->getID() . ' - ' . date('r', $event->getStartDate()) . ' // ' . $event->getWeeklyCode() . "\n";
	}
	echo "\n\n";

	echo 'Monthly...' . "\n";
	$iter = new RecurringEventIterator('Monthly');
	while ($iter->hasNext())
	{
		$event = $iter->getNext();
		echo $event->getID() . ' - ' . date('r', $event->getStartDate()) . "\n";
	}
	echo "\n\n";

	echo 'Yearly...' . "\n";
	$iter = new RecurringEventIterator('Yearly');
	while ($iter->hasNext())
	{
		$event = $iter->getNext();
		echo $event->getID() . ' - ' . date('r', $event->getStartDate()) . "\n";
	}
	echo "\n\n";
	
	echo "Done\n";
?>
