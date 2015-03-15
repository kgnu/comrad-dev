#!/usr/bin/php
<?php

	require_once('../classes/Initialize.php');
        $init = new Initialize();
	$init->setAutoload();



	$events = DB::getInstance('MySql');

	$psa = new PSA(array( 'UID' => 2 ));
	echo $psa->UID;

	$meta = $events->get($psa);

	print_r($meta);

?>
