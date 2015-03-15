#!/usr/bin/php
<?php
	require_once('../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();

	echo "UNIT TEST: RoleIterator\n";

	$iter = new RoleIterator();
	while ($iter->hasNext())
	{
		echo $iter->getNext() . "\n";
	}

	echo "Done\n";
?>
