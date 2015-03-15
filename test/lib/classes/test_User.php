#!/usr/bin/php
<?php
	require_once('../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();

	echo "UNIT TEST: User\n";


	echo " Testing the creation of a new user...\t";
	$user = new User('alsdkfj');
	assert($user->populate() == false);	// User shouldn't exist yet
	$user->update();
	$user2 = new User('alsdkfj', $user);
	assert($user->populate() == true);	// User should exist
	echo "OK\n";


	echo " Testing the removal of a user...\t";
	$user3 = new User('alsdkfj', $user);
	assert($user3->populate() == true);	// User should still exist
	$user3->remove();
	$user4 = new User('alsdkfj', $user);
	assert($user4->populate() == false);	// User should have been removed
	echo "OK\n";


	echo "Done\n";
?>
