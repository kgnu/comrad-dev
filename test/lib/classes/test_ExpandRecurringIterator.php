#!/usr/bin/php
<?php
	require_once('../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();

	echo "UNIT TEST: ExpandRecurringIterator\n";



	echo "---------------------------------------------------------\n";
	$dailyEvent = new RecurringEventDaily(1);
	$dailyEvent_exists = $dailyEvent->populate();
	if ($dailyEvent_exists)
	{
		echo $dailyEvent_exists ? 'exists' : 'not found';
		echo "\n";


		echo 'Start: ' . date('r', $dailyEvent->getStartDate()) . "\n";
		echo 'starting loop...' . "\n";
		echo "-\n";
		$iter1 = new ExpandRecurringIterator($dailyEvent);
		while ($iter1->hasNext())
		{
			echo date('r', $iter1->getNext()) . "\n";
		}
		echo "-\n";
	}
	else
	{
		echo "No daily events found\n";
	}



	echo "---------------------------------------------------------\n";

	$weeklyEvent = new RecurringEventWeekly(1);
	$weeklyEvent_exists = $weeklyEvent->populate();
	if ($weeklyEvent_exists)
	{
		echo $weeklyEvent_exists ? 'exists' : 'not found';
		echo "\n";


		echo 'Start: ' . date('r', $weeklyEvent->getStartDate()) . "\n";
		echo 'starting loop...' . "\n";
		echo "-\n";
		$iter2 = new ExpandRecurringIterator($weeklyEvent);
		while ($iter2->hasNext())
		{
			echo date('r', $iter2->getNext()) . "\n";
		}
		echo "-\n";
	}
	else
	{
		echo "No weekly events found\n";
	}






	echo "---------------------------------------------------------\n";

	$monthlyEvent = new RecurringEventMonthly(1);
	$monthlyEvent_exists = $monthlyEvent->populate();
	if ($monthlyEvent_exists)
	{
		echo $monthlyEvent_exists ? 'exists' : 'not found';
		echo "\n";


		echo 'Start: ' . date('r', $monthlyEvent->getStartDate()) . "\n";
		echo 'starting loop...' . "\n";
		echo "-\n";
		$iter3 = new ExpandRecurringIterator($monthlyEvent);
		while ($iter3->hasNext())
		{
			echo date('r', $iter3->getNext()) . "\n";
		}
		echo "-\n";
	}
	else
	{
		echo "No monthly events found\n";
	}



	echo "---------------------------------------------------------\n";


	$yearlyEvent = new RecurringEventYearly(1);
	$yearlyEvent_exists = $yearlyEvent->populate();
	if ($yearlyEvent_exists)
	{
		echo $yearlyEvent_exists ? 'exists' : 'not found';
		echo "\n";


		echo 'Start: ' . date('r', $yearlyEvent->getStartDate()) . "\n";
		echo 'starting loop...' . "\n";
		echo "-\n";
		$iter4 = new ExpandRecurringIterator($yearlyEvent);
		while ($iter4->hasNext())
		{
			echo date('r', $iter4->getNext()) . "\n";
		}
		echo "-\n";
	}
	else
	{
		echo "No yearly events found\n";
	}







	echo "\nDone\n";
?>
