<?php

	require_once('initialize.php');
	require_once('recaptcha/recaptchalib-1.10.php');

	// Check if the user is active...
	if ($init->isActiveCodeValid((array_key_exists('Active', $_SESSION) ? $_SESSION['Active'] : null)))
	{
		// See if the user is trying to sign out...
		if ($uri->getKeyAsBool('signout'))
		{
			PermissionManager::getInstance()->disableAuthorization();
			$results = DB::getInstance('MySql')->find(new User(array('Username' => $_SESSION['Username'])));
			$user = $results[0];
			if ($user) $user->markLastVisit();
			PermissionManager::getInstance()->enableAuthorization();

			// Clean up...
			$init->log('User \'' . $_SESSION['Username'] . '\' signed out');
			session_destroy();
			$uri->clearKeys();
			$uri->redirect();
		}

		// Not trying to sign out, jump to contents...
		$jump_uri = new UriBuilder('contents.php');
		$jump_uri->redirect();
	}

	// Validate the user since (at this point) they're not active...
	if ( $uri->hasKey('username') && $uri->hasKey('password') )
	{
		// Manage sign in attempts...
		if (!array_key_exists('SignInAttempts', $_SESSION)) $_SESSION['SignInAttempts'] = 1; else $_SESSION['SignInAttempts']++;

		// Make sure we have some username and password...
		if ($uri->getKey('username') == '' || $uri->getKey('password') == '')
		{
			$uri->removeKey('password');
			$uri->removeKey('code');
			$uri->updateKey('result', '0');
			$uri->redirect('get');
		}

		// Validate the code only if we exceeded sign in attempts. If it isn't
		//  right, bail...
		if ((array_key_exists('SignInAttempts', $_SESSION) ? $_SESSION['SignInAttempts'] : 0) > $init->getProp('Admin_SignInAttempts'))
		{
			$reCaptcha = recaptcha_check_answer($init->getProp('ReCaptcha_PrivateKey'), $_SERVER['REMOTE_ADDR'], $uri->getKey('recaptcha_challenge_field'), $uri->getKey('recaptcha_response_field'));
			if (!$reCaptcha->is_valid)
			{
				$init->log('Attempted sign in for user \'' . $uri->getKey('username') . '\', (time ' . $_SESSION['SignInAttempts'] . ')');
				$uri->removeKey('password');
				$uri->removeKey('recaptcha_challenge_field');
				$uri->removeKey('recaptcha_response_field');
				$uri->updateKey('result', '1');
				$uri->redirect('get');
			}
		}

		// Connect to the user...
		PermissionManager::getInstance()->disableAuthorization();
		$results = DB::getInstance('MySql')->find(new User(array('Username' => $uri->getKey('username'))));
		PermissionManager::getInstance()->enableAuthorization();
		$user = $results[0];

		// Make sure their password is correct...
		if ($user && $user->encryptPassword($uri->getKey('password')) == $user->PasswordHash)
		{
			// User is valid, prepare session...
			$_SESSION['SignOnTime'] = time();
			$_SESSION['Username'] = $uri->getKey('username');
			$_SESSION['UserId'] = $user->Id;
			$_SESSION['RoleId'] = $user->RoleId;
			$_SESSION['Active'] = $init->genActiveCode();
			$_SESSION['LastActive'] = time();
			$_SESSION['Role'] = $user->RoleId;
			$_SESSION['Shared'] = $user->Shared;

			// Log success...
			$init->log('Sign in for user \'' . $uri->getKey('username') . '\' accepted');

			// Jump to entrance...
			if (isset($_SESSION['JumpBack']))
			{
				$jump_to = $_SESSION['JumpBack'];
				unset($_SESSION['JumpBack']);
				$jump_uri = new UriBuilder($jump_to);
				$jump_uri->redirect();
			}
			else if  ($_SESSION["RoleId"] == 7) //TODO: rather than having this hardcoded, add a default URL parameter to the Role table and use that
			{
				$jump_uri = new UriBuilder('resources.php');
				$jump_uri->redirect();
			}
			else
			{
				$jump_uri = new UriBuilder('contents.php');
				$jump_uri->redirect();
			}
		}
		else
		{
			$uri->removeKey('password');
			$uri->removeKey('code');
			$uri->updateKey('result', '0');
			$uri->redirect('get');
		}
	}

?>
<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();                                   # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

<title>comrad: <?php echo $init->getProp('Organization_Name'); ?></title>

<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();                                   # ?>
<?php $body->setShowSessionTools(false);                                   # ?>
<?php $body->setShowHeaderNav(false);                                      # ?>
<?php $body->setShowSignOut(false);                                        # ?>
<?php $body->setFootHeight(18);                                            # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>


	<center>
	<table cellpadding="0" cellspacing="0" border="0" height="50%" style="margin: 0px;"><tr><td valign="bottom">
	<table cellpadding="0" cellspacing="0" border="0" style="margin: 0px;"><tr><td>
	

	<h4><?php echo $init->getProp('WebTools_Name'); ?></h4>


	<?php if ($uri->getKey('result') == '0') { ?>
		<div id="notice" class="errorBox" style="margin-left: 0px;">
		<div class="content">
		The <b>username</b> and/or <b>password</b> you have entered was <b>invalid</b>. Your information has been logged for further evaluation.<br /><br />
		<?php echo '<b>Logged:</b> ' . date(DATE_RFC822) . ' via ' . $_SERVER['REMOTE_ADDR']; ?>
		</div>
		</div>

	<?php } elseif ($uri->getKey('result') == '1') { ?>
		<div id="notice" class="errorBox" style="margin-left: 0px;">
		<div class="content">
		The <b>security code</b> you have entered was <b>invalid</b>. Please try again with the new security code provided.
		</div>
		</div>

	<?php } elseif ($uri->getKey('result') == '2') { ?>
		<div id="notice" class="errorBox" style="margin-left: 0px;">
		<div class="content">
		Your selected role no longer exists or is invalid. Please check with your administrator to resolve this issue.
		</div>
		</div>

	<?php } ?>


	<form name="signin" action="index.php" method="POST">

	<table cellpadding="0" cellspacing="0" border="0">
	<tr><td width="120"><b>Username:</b></td><td width="400"><input type="text" name="username" value="<?= $uri->getKey('username') ?>" class="signInUsername" /></td></tr>
	<tr><td width="120"><b>Password:</b></td><td width="400"><input type="password" name="password" class="signInPassword" /></td></tr>

	<?php if ((array_key_exists('SignInAttempts', $_SESSION) ? $_SESSION['SignInAttempts'] : 0) >= $init->getProp('Admin_SignInAttempts')) { ?>
		<tr>
		<td width="120"><b>Security:</b></td>
		<td width="400">
			<script>var RecaptchaOptions = { theme: 'white' };</script>
			<?php echo recaptcha_get_html($init->getProp('ReCaptcha_PublicKey')); ?>
		</td>
		</tr>
	<?php } ?>

	<tr><td width="120">&nbsp;</td><td width="400">&nbsp;</td></tr>
	<tr><td width="120">&nbsp;</td><td width="400"><input type="submit" value="Sign In" class="signInButton" /></td></tr>
	</table>

	</form>


	</td></tr></table>
	</td></tr></table>
	</center>


	<script language="javascript">
	<?php if ($uri->getKey('username') != '') { ?>
		document.signin.password.focus();
	<?php } else { ?>
		document.signin.username.focus();
	<?php } ?>
	</script>


<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();                                 # ?>
<?php $close->setShowFooterNav(false);                                     # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
