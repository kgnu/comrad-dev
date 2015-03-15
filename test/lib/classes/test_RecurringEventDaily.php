#!/usr/bin/php
<?php
	require_once('../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();

	echo "UNIT TEST: RecurringEventDaily\n";


	$dre = new RecurringEventDaily(1);
	$dre_exists = $dre->populate();
	echo $dre_exists ? 'yes' : 'no';
	echo "\n";

	if ($dre_exists)
	{
		echo 'ID: ' . $dre->getID() . "\n";
		echo 'ParentInstanceID: ' . $dre->getParentInstanceID() . "\n";
		echo 'StartDate: ' . date('r', $dre->getStartDate()) . "\n";
		echo 'EndDate: ' . date('r', $dre->getEndDate()) . "\n";
		echo 'EveryXDays: ' . $dre->getEveryXDays() . "\n";
	}

	echo "Done\n";
?>
