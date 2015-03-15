<?php

	require_once('initialize.php');

	// Check permissions...
	$perm = new Permission($_SESSION['PermActivityLog']);
	if ($_SESSION['Username'] != 'root' && !$perm->hasRainbow())
	{
		$jump_uri = new UriBuilder('denied.php');
		$jump_uri->updateKey('from', 'ActivityLog');
		$jump_uri->redirect('get');
	}

	// Download the log file...
	if ($uri->getKey('cmd') == 'downloadlog')
	{
		$file_path = $init->getProp('Log_Admin');
		$file_name = basename($file_path);
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Length: ' . filesize($file_path));
		header('Content-Disposition: attachment; filename="' . $file_name . '"');
		readfile($file_path);
		exit();
	}

?>
<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();                                   # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Activity and Debug Log</title>

<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();                                   # ?>
<?php //$body->setPathPageIcon('media/activity-log.png');                    # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>
	
	<h4>Activity and Debug Log</h4>

	<div style="margin-left: 20px;">
	<textarea id="log" style="width: 100%; height: 400px; background-color: #eeeeee; border: 1px solid #c0c0c0;"><?php

		$output = $init->getProp('Log_Admin');
		$output = `tail -n 500 $output`;
		echo $output;

	?></textarea>
	</div>

	<p><a href="log.php?cmd=downloadlog">Download Full Log</a> 
	(<?php echo round(filesize($init->getProp('Log_Admin')) / 1024.0 / 1024.0, 2) . ''; ?> MB)</p>

	<script type="text/javascript">
		var textArea = document.getElementById('log');
		textArea.scrollTop = textArea.scrollHeight;
	</script>

<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();                                 # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
