<?php require_once('initialize.php'); ?>
<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();                                   # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; About</title>

<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();                                   # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>

	<h4>About</h4>
	
	<p>More information will be available soon!</p>

	<h4>How can I get help?</h4>

	<p>This section explains how users can get help with the comrad system.</p>

<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();                                 # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
