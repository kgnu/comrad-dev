#!/usr/bin/php
<?php
	require_once('../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();

	echo "UNIT TEST: RecurringEventWeekly\n";


	$dre = new RecurringEventWeekly(3);
	$dre_exists = $dre->populate();
	echo $dre_exists ? 'yes' : 'no';
	echo "\n";

	if ($dre_exists)
	{
		echo 'ID: ' . $dre->getID() . "\n";
		echo 'ParentInstanceID: ' . $dre->getParentInstanceID() . "\n";
		echo 'StartDate: ' . date('r', $dre->getStartDate()) . "\n";
		echo 'EndDate: ' . date('r', $dre->getEndDate()) . "\n";
		echo 'EveryXWeeks: ' . $dre->getEveryXWeeks() . "\n";
		echo 'WeeklyCode: ' . $dre->getWeeklyCode() . "\n";
	}
	else
	{
		$dre->setParentInstanceID(1);
		$dre->setStartDate(time());
		$dre->setEndDate(time());
		$dre->setEveryXWeeks(4);
		$dre->setMonday(true);
		$dre->setWednesday(true);
		$dre->setFriday(true);
		$dre->update();
	}

	echo "Done\n";
?>
