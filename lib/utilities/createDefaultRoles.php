#!/usr/bin/php
<?php

	require_once('../classes/Initialize.php');
	$init = new Initialize();
	$init->setAutoload();


	$role = new Role('DJ');
	$role_exists = $role->populate();
	$role->addPermission('users', new Permission());
	$role->addPermission('showbuilder', new Permission('vcmr'));
	$role->addPermission('calendarview', new Permission('v---'));
	$role->addPermission('tracks', new Permission());
	$role->addPermission('phpmyadmin', new Permission());
	$role->update();

	$role = new Role('Manager');
	$role_exists = $role->populate();
	$role->addPermission('users', new Permission('v---'));
	$role->addPermission('showbuilder', new Permission('vcmr'));
	$role->addPermission('calendarview', new Permission('vcmr'));
	$role->addPermission('tracks', new Permission('vcmr'));
	$role->addPermission('phpmyadmin', new Permission());
	$role->update();

	$role = new Role('Administrator');
	$role_exists = $role->populate();
	$role->addPermission('users', new Permission('vcmr'));
	$role->addPermission('showbuilder', new Permission('vcmr'));
	$role->addPermission('calendarview', new Permission('vcmr'));
	$role->addPermission('tracks', new Permission('vcmr'));
	$role->addPermission('phpmyadmin', new Permission('vcmr'));
	$role->update();

	echo "done!!!!!!!!!!!11111\n";

?>
