#!/usr/bin/php
<?php
	require_once('../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();

	echo "UNIT TEST: UserIterator\n";


	echo " Create some test users...\t";
	$userA = new User('zzzzzzA');
	assert($userA->populate() == false);
	$userA->update();
	$userB = new User('zzzzzzB', $userA);
	assert($userB->populate() == false);
	$userB->update();
	$userC = new User('zzzzzzC', $userA);
	assert($userC->populate() == false);
	$userC->update();
	echo "OK\n";


	echo " Test iteration...\t\t";
	$iter = new UserIterator($userA);
	$started = false;
	while ($iter->hasNext())
	{
		$user = $iter->getNext();

		// See if we should start...
		if ($user->getUsername() == 'zzzzzzA') $started = true;

		// Handle start...
		if ($started)
		{
			assert($iter->getNext()->getUsername() == 'zzzzzzB');
			assert($iter->getNext()->getUsername() == 'zzzzzzC');
			break;
		}
	}
	echo "OK\n";


	echo " Remove test users...\t\t";
	$userA->remove();
	$userB->remove();
	$userC->remove();
	echo "OK\n";


	echo "Done\n";
?>
