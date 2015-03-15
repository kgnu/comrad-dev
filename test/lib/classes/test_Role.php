#!/usr/bin/php
<?php
	require_once('../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();

	echo "UNIT TEST: Role\n";

	$role = new Role(1);
	assert($role->populate() == true);

	echo "Done\n";
?>
