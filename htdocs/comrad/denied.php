<?php

	require_once('initialize.php');
	
	// Define the service name...
	switch ($uri->getKey('from'))
	{
		case 'catalog.php':		$service = 'Catalog Management'; break;
		case 'changemypassword.php':	$service = 'Change My Password'; break;
		case 'djshow.php':		$service = 'DJ Show'; break;
		case 'events.php':		$service = 'Events Management'; break;
		case 'log.php':			$service = 'Activity and Debug Log'; break;
		case 'roles.php':		$service = 'Role Management'; break;
		case 'schedule.php':		$service = 'Schedule Management'; break;
		case 'users.php':		$service = 'User Management'; break;
		case 'phpmyadmin.php':		$service = 'phpMyAdmin'; break;
	}

	// Make sure we have a service...
	if (!isset($service))
	{
		$jumpUri = new UriBuilder('contents.php');
		$jumpUri->redirect();
	}

	// Log permission error...
	$init->log('Permission denied to ' . $service);

?>
<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();                                   # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Permission Denied</title>

<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();                                   # ?>
<?php $body->setPathPageIcon('media/denied.png');                          # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>
	
	<h4>Permission Denied</h4>

	<p>You do not have permission to access <b><?= $service ?></b>. Please 
	contact your system administrator <a href="mailto:<?= $init->getProp('WebTools_SysAdmin_Email')?>"><?= $init->getProp('WebTools_SysAdmin_Name')?></a>
	to request elevation.</p>

<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();                                 # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
