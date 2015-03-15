<?php

	require_once('initialize.php');

	// If we have root user, show a warning...
	$rootUserWarning = $_SESSION['Username'] == 'root';

	// Check if they clicked cancel...
	if ($uri->hasKey('cancel'))
	{
		$jump_uri = new UriBuilder('contents.php');
		$jump_uri->redirect();
	}

	// Execute the change command...
	if ($uri->getKeyAsBool('execute'))
	{
		// Password cannot be empty...
		if ($uri->getKey('password0') == '')
			{ $uri->clearKeys(); $uri->updateKey('result', 'password_empty'); $uri->redirect('get'); }
			
		// Password must be 0 or between 6 and 80 characters long...
		if ( strlen($uri->getKey('password0')) != 0 && (strlen($uri->getKey('password0')) < 6 || strlen($uri->getKey('password0')) > 80) )
			{ $uri->clearKeys(); $uri->updateKey('result', 'password_wrong_length'); $uri->redirect('get'); }

		// Password may only contain valid characters in range !, #-&, (-~...
		if (strlen($uri->getKey('password0')) != 0 && !preg_match("/^[!#-&(-~]+$/", $uri->getKey('password0')))
			{ $uri->clearKeys(); $uri->updateKey('result', 'password_invalid'); $uri->redirect('get'); }

		// Passwords must match for confirmation...
		if ($uri->getKey('password0') != $uri->getKey('password1'))
			{ $uri->clearKeys(); $uri->updateKey('result', 'password_mismatch'); $uri->redirect('get'); }

		// Change user password...
		PermissionManager::getInstance()->disableAuthorization();
		$results = DB::getInstance('MySql')->find(new User(array('Username' => $_SESSION['Username'])));
		$user = $results[0];
		$user->PasswordHash = $user->encryptPassword($uri->getKey('password0'));
		DB::getInstance('MySql')->update($user);
		PermissionManager::getInstance()->enableAuthorization();

		// Save log...
		$init->log('User has changed their password.');

		// Redirect to success...
		$uri->clearKeys();
		$uri->updateKey('result', 'success');
		$uri->redirect('get');
	}

?>
<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();                                   # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Change My Password</title>

<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();                                   # ?>
<?php $body->setPathPageIcon('media/users.png');                           # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>

	<?php
	$ret = '';
	switch ($uri->getKey('result'))
	{
		case 'password_empty':			$ret = '<b class="error">Error:</b> The password field must not be left empty.'; break;
		case 'password_wrong_length':	$ret = '<b class="error">Error:</b> Passwords must be between 6 and 80 characters long.'; break;
		case 'password_invalid':		$ret = '<b class="error">Error:</b> Passwords may <b>not</b> contain spaces, quotes, or other "white-space" characters.'; break;
		case 'password_mismatch':		$ret = '<b class="error">Error:</b> The passwords you typed do not match. Please retype them exactly alike in order to confirm your password selection.'; break;

		case 'success':					$ret = '<b>Success:</b> Your password has been changed!'; break;
	}
	?>

	<h4>Change My Password</h4>

	<?php if ($ret != '') { ?>
	<div id="notice" class="<?= substr($ret, 0, 15) == '<b>Success:</b>' ? 'info' : 'error' ?>Box">
	<div class="content"><?php echo $ret; ?></div>
	</div>
	<?php } ?>

	<?php if ($rootUserWarning): ?>
		<p>This is the <b>root</b> user account. This account has access to all aspects of the
		system. It may not be changed or removed. It is recommended that you change
		its password regularly (below) to ensure maximum security of the system.</p>
	<?php else: ?>
		<p>It is recommended that you change your password regularly to ensure
		maximum security of the Web Tools system.</p>
	<?php endif; ?>

	<form name="changemypassword" action="changemypassword.php" method="POST">
	<input type="hidden" name="execute" value="1" />

	<table cellpadding="0" cellspacing="0" border="0">

	<tr><td width="120"><b>Password:</b></td>
	<td><input type="password" name="password0" value="" style="width: 300px;" /></td></tr>

	<tr><td colspan="2"><img src="../media/inviso.gif" width="1" height="4" /></td></tr>

	<tr><td width="120"><b>Password:</b><br /><i style="font-size: 10px;">(Again for Verification)</i></td>
	<td><input type="password" name="password1" value="" style="width: 300px;" /></td></tr>

	<tr><td colspan="2"><img src="../media/inviso.gif" width="1" height="8" /></td></tr>

	<tr><td width="120">&nbsp;</td><td>
	<input type="submit" value="Change My Password" class="button" onClick="javascript:this.disabled = true; document.changemypassword.submit();" />
	<input type="submit" name="cancel" value="Cancel" class="button" />
	</td></tr>

	</table>

	</form>

	<script language="javascript">
	document.changemypassword.password0.focus();
	</script>

<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();                                 # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
