<?php

	require_once('initialize.php');

	// Check if user is trying to modify him or herself...
	if ( ($_SESSION['Username'] != 'root') && 
		($uri->getKey('username') == $_SESSION['Username']) &&
		($uri->getKey('cmd') == 'modify') )
	{
		$uri->clearKeys();
		$uri->updateKey('result', 'current_user_cannot_modify');
		$uri->redirect('get');
	}

	// Check if they clicked cancel...
	if ($uri->hasKey('cancel'))
	{
		$uri->clearKeys();
		$uri->redirect('get');
	}

	// Check if user wants to manage roles...
	if ($uri->hasKey('jumpToUserRoles'))
	{
		$jumpUri = new UriBuilder('roles.php');
		$jumpUri->redirect();
	}

	// Validate and execute the create, modify, or remove command...
	if ($uri->getKeyAsBool('execute')) switch ($uri->getKey('cmd'))
	{
		case 'create':
			// A username must be given...
			if ($uri->getKey('username') == '')
				{ $uri->removeKey('execute'); $uri->updateKey('result', 'user_empty'); $uri->redirect(); }
			
			// Given username may only contain valid characters in range letters, numbers, no space...
			if (!preg_match("/^[a-zA-Z0-9]+$/", $uri->getKey('username')))
				{ $uri->removeKey('execute'); $uri->updateKey('result', 'user_invalid'); $uri->redirect(); }
				
			// Given username must not be already taken...
			if (count(DB::getInstance('MySql')->find(new User(array('Username' => $uri->getKey('username'))))) > 0)
				{ $uri->removeKey('execute'); $uri->updateKey('result', 'user_exists'); $uri->redirect(); }

			// A password must be given...
			if ($uri->getKey('password0') == '')
				{ $uri->removeKey('execute'); $uri->updateKey('result', 'password_empty'); $uri->redirect(); }
			
		case 'modify':
			
			// If modifying user, an Id must be provided...
			if ($uri->getKey('cmd') == 'modify' && (!$uri->hasKey('id') || $uri->getKey('id') == ''))
				{ $uri->removeKey('execute'); $uri->updateKey('result', 'id_empty'); $uri->redirect(); }
			
			// If modifying user, he or she must already exist...
			if ($uri->getKey('cmd') == 'modify' && DB::getInstance('MySql')->get(new User(array('Id' => $uri->getKey('id')))) == null)
				{ $uri->removeKey('execute'); $uri->updateKey('result', 'user_not_exists'); $uri->redirect(); }

			// Password must be 0 or between 6 and 80 characters long...
			if ( strlen($uri->getKey('password0')) != 0 && (strlen($uri->getKey('password0')) < 6 || strlen($uri->getKey('password0')) > 80) )
				{ $uri->removeKey('execute'); $uri->updateKey('result', 'password_wrong_length'); $uri->redirect(); }

			// Password may only contain valid characters in range !, #-&, (-~...
			if (strlen($uri->getKey('password0')) != 0 && !preg_match("/^[!#-&(-~]+$/", $uri->getKey('password0')))
				{ $uri->removeKey('execute'); $uri->updateKey('result', 'password_invalid'); $uri->redirect(); }

			// Passwords must match for confirmation...
			if ($uri->getKey('password0') != $uri->getKey('password1'))
				{ $uri->removeKey('execute'); $uri->updateKey('result', 'password_mismatch'); $uri->redirect(); }

			// Save user parameters...
			$userToSave = new User();
			if ($uri->getKey('cmd') == 'modify') $userToSave->Id = $uri->getKey('id');
			$userToSave->Username = $uri->getKey('username');
			if ($uri->getKey('password0') != '') $userToSave->PasswordHash = $userToSave->encryptPassword($uri->getKey('password0'));
			$userToSave->Shared = $uri->getKey('shared');
			$userToSave->RoleId = $uri->getKey('role');
			
			if ($userToSave->isNew()) {
				DB::getInstance('MySql')->insert($userToSave);
			} else {
				DB::getInstance('MySql')->update($userToSave);
			}
			
			// Save log...
			$init->log('User \'' . $uri->getKey('username') . '\' ' . ($uri->getKey('cmd') == 'create' ? 'created' : 'modified'));
			
			// Redirect to success...
			$cmd = $uri->getKey('cmd');
			$username = $uri->getKey('username');
			$uri->clearKeys();
			$uri->updateKey('cmd', 'view');
			$uri->updateKey('result', "{$cmd}_success");
			$uri->updateKey('username', $username);
			$uri->redirect();

			break;

		case 'remove':
			
			// If modifying user, an Id must be provided...
			if (!$uri->hasKey('id') || $uri->getKey('id') == '')
				{ $uri->removeKey('execute'); $uri->updateKey('result', 'id_empty'); $uri->redirect(); }
			
			// If modifying user, he or she must already exist...
			if (DB::getInstance('MySql')->get(new User(array('Id' => $uri->getKey('id')))) == null)
				{ $uri->removeKey('execute'); $uri->updateKey('result', 'user_not_exist_for_removal'); $uri->redirect(); }

			// The root account cannot be removed...
			if ($uri->getKey('username') == 'root')
				{ $uri->removeKey('execute'); $uri->updateKey('result', 'root_cannot_remove'); $uri->redirect(); }
			
			// Make sure we're not logged in as this user...
			if ($uri->getKey('username') == $_SESSION['Username'])
				{ $uri->removeKey('execute'); $uri->updateKey('result', 'current_user_cannot_remove'); $uri->redirect(); }

			// Remove the user from the database...
			DB::getInstance('MySql')->delete(new User(array('Id' => $uri->getKey('id'))));
			$init->log('User \'' . $uri->getKey('username') . '\' removed');

			// Redirect to success...
			$username = $uri->getKey('username');
			$uri->clearKeys();
			$uri->updateKey('cmd', 'view');
			$uri->updateKey('result', 'remove_success');
			$uri->updateKey('username', $username);
			$uri->redirect();

			break;
	}
	
?>
<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();								   # ?>
<?php $head->write();													   # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; User Management</title>

<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();								   # ?>
<?php $body->setPathPageIcon('media/users.png');						   # ?>
<?php $body->write();													   # ?>
<?php ###################################################################### ?>

	<?php
	$ret = '';
	switch ($uri->getKey('result'))
	{
		case 'id_empty':					$ret = '<b class="error">Error:</b> User Id is missing.'; break;
		case 'user_empty':					$ret = '<b class="error">Error:</b> The username field cannot be left blank.'; break;
		case 'user_invalid':				$ret = '<b class="error">Error:</b> Username may <b>only</b> contain letters and numbers. Spaces, quotes, or other "white-space" characters are <b>not</b> allowed.'; break;
		case 'user_exists':					$ret = '<b class="error">Error:</b> The user <b>' . $uri->getKey('username') . '</b> you are trying to create already exists.'; break;
		case 'password_empty':				$ret = '<b class="error">Error:</b> The password field must not be left empty.'; break;
		case 'user_not_exists':				$ret = '<b class="error">Error:</b> The user <b>' . $uri->getKey('username') . '</b> you are trying to modify does not exist.'; break;
		case 'password_wrong_length':		$ret = '<b class="error">Error:</b> Passwords must be between 6 and 80 characters long.'; break;
		case 'password_invalid':			$ret = '<b class="error">Error:</b> Passwords may <b>not</b> contain spaces, quotes, or other "white-space" characters.'; break;
		case 'password_mismatch':			$ret = '<b class="error">Error:</b> The passwords you typed do not match. Please retype them exactly alike in order to confirm your password selection.'; break;

		case 'user_not_exist_for_removal':	$ret = '<b class="error">Error:</b> The user <b>' . $uri->getKey('username') . '</b> you are trying to remove does not exist.'; break;
		case 'current_user_cannot_remove':	$ret = '<b class="error">Error:</b> You are currently signed in as <b>' . $uri->getKey('username') . '</b>. Please remove this user via another account (i.e. the <b>root</b> account).'; break;
		case 'current_user_cannot_modify':	$ret = '<b class="error">Error:</b> You are currently signed in as <b>' . $_SESSION['Username'] . '</b>. Please modify this user via another account (i.e. the <b>root</b> account).'; break;
		case 'root_cannot_remove':			$ret = '<b class="error">Error:</b> As a safety precaution, the user <b>root</b> cannot be removed from this system.'; break;

		case 'create_success':				$ret = '<b>Success:</b> You have created the user <i>' . $uri->getKey('username') . '</i>.'; break;
		case 'modify_success':				$ret = '<b>Success:</b> You have modifed the user <i>' . $uri->getKey('username') . '</i>.'; break;
		case 'remove_success':				$ret = '<b>Success:</b> You have removed the user <i>' . $uri->getKey('username') . '</i>.'; break;
	}
	?>

	<?php if ($uri->getKey('cmd') == 'create' || $uri->getKey('cmd') == 'modify') { ?>
		
		<?php $user = ($uri->getKey('id') ? DB::getInstance('MySql')->get(new User(array('Id' => $uri->getKey('id')))) : null); ?>
		
		<h4><?= ucwords($uri->getKey('cmd')) ?> User</h4>

		<?php if ($ret != '') { ?>
		<div id="notice" class="<?= substr($ret, 0, 15) == '<b>Success:</b>' ? 'info' : 'error' ?>Box">
		<div class="content"><?php echo $ret; ?></div>
		</div>
		<?php } ?>

		<?php if ($uri->getKey('cmd') == 'modify' && $user->Username == 'root') { ?>
		<p>This is the <b>root</b> user account. This account has access to all aspects of the
		system. It may not be changed or removed. It is recommended that you change
		its password regularly (below) to ensure maximum security of the system.</p>
		<?php } ?>

		<form name="user" action="users.php" method="POST">
			<input type="hidden" name="cmd" value="<?= $uri->getKey('cmd') ?>" />
			<?php if ($user): ?>
			<input type="hidden" name="id" value="<?= $user->Id ?>" />
			<?php endif; ?>
			<input type="hidden" name="execute" value="1" />

			<table cellpadding="0" cellspacing="0" border="0">

			<tr>
			<td width="120"><b>Username:</b></td>
			<td>
			<?php if ($user): ?>
				<input type="hidden" name="username" value="<?= $user->Username ?>" /> <?= $user->Username ?>
			<?php else: ?>
				<input type="text" name="username" value="" style="width: 300px;" />
			<?php endif; ?>
			</td>
			</tr>

			<tr><td colspan="2"><img src="media/inviso.gif" width="1" height="4" /></td></tr>

			<tr><td width="120"><b>Password:</b></td>
			<td><input type="password" name="password0" value="" style="width: 300px;" /></td></tr>

			<tr><td colspan="2"><img src="media/inviso.gif" width="1" height="4" /></td></tr>
		
			<tr><td width="120"><b>Password:</b><br /><i style="font-size: 10px;">(Again for Verification)</i></td>
			<td><input type="password" name="password1" value="" style="width: 300px;" /></td></tr>

			<tr><td colspan="2"><img src="media/inviso.gif" width="1" height="8" /></td></tr>
		
			<tr><td width="120"><b>Shared:</b><br /><i style="font-size: 10px;">(Is this a shared login?)</i></td>
			<td><input type="checkbox" name="shared"<?php if ($user && $user->Shared == true) echo ' checked="checked"' ?> /></td></tr>

			<tr><td colspan="2"><img src="media/inviso.gif" width="1" height="8" /></td></tr>

			<?php if ($uri->getKey('cmd') == 'create' || $uri->getKey('username') != 'root'): ?>
				<tr>
					<td width="120"><b>Role:</b></td>
					<td>
						<?php $roles = DB::getInstance('MySql')->find(new Role()); ?>
						<select name="role">
						<?php foreach ($roles as $role): ?>
							<option value="<?= $role->Id ?>"<?php if ($user && $user->RoleId == $role->Id) echo ' selected="selected"' ?>><?= $role->Name ?></option>
						<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr><td colspan="2"><img src="media/inviso.gif" width="1" height="16" /></td></tr>
			<?php endif; ?>

			<tr><td width="120">&nbsp;</td><td>
			<input type="submit" value="<?= ucwords($uri->getKey('cmd')) ?> User" class="button" onClick="javascript:this.disabled = true; document.user.submit();" />
			<input type="submit" name="cancel" value="Cancel" class="button" />
			</td></tr>

			</table>

		</form>

		<?php if ($uri->getKey('cmd') == 'create') { ?>
		<script language="javascript">
		document.user.username.focus();
		</script>
		<?php } ?>

	<?php } else if ($uri->getKey('cmd') == 'remove') { ?>

		<?php $user = ($uri->getKey('id') ? DB::getInstance('MySql')->get(new User(array('Id' => $uri->getKey('id')))) : null); ?>

		<h4>Remove User</h4>

		<?php if ($ret != '') { ?>
		<div id="notice" class="<?= substr($ret, 0, 15) == '<b>Success:</b>' ? 'info' : 'error' ?>Box">
		<div class="content"><?php echo $ret; ?></div>
		</div>
		<?php } ?>

		<p>Are you sure you would like to remove user <b><?= $user->Username ?></b>?</p>

		<table cellpadding="0" cellspacing="0" border="0"><tr><td>
		<form action="users.php" method="GET">
		<input type="hidden" name="cmd" value="remove" />
		<input type="hidden" name="execute" value="1" />
		<input type="hidden" name="id" value="<?= $user->Id ?>" />
		<input type="submit" value="Remove User: <?= $user->Username ?>" class="button" />
		<input type="submit" name="cancel" value="Cancel" class="button" />
		</form></td></tr></table>

	<?php } else { ?>

		<h4>Users</h4>

		<?php if ($ret != '') { ?>
		<div id="notice" class="<?= substr($ret, 0, 15) == '<b>Success:</b>' ? 'info' : 'error' ?>Box">
		<div class="content"><?php echo $ret; ?></div>
		</div>
		<?php } ?>

		<div class="tableHeading" style="width: 410px;">Users</div>

		<div class="tableSubheading" style="width: 410px;">
		<table cellpadding="0" cellspacing="0" border="0"><tr>
		<td width="33">&nbsp;</td>
		<td width="236"><b>Username</b></td>
		<td width="131"><b>Options...</b></td>
		</tr></table>
		</div>

		<?php $users = DB::getInstance('MySql')->find(new User()); ?>
		<?php $count = 1; ?>
		<div class="tableBody" style="width: 410px;">
			<?php foreach($users as $user): ?>
			<div class="tableRow">
				<table cellpadding="0" cellspacing="0" border="0"><tr style="vertical-align: top;">
				<td width="33"><?= $count ?>.</td>
				<td width="236"><?= $user->Username ?></td>
				<td width="131">
					<a href="users.php?cmd=modify&id=<?= $user->Id ?>">Modify</a>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="users.php?cmd=remove&id=<?= $user->Id ?>">Remove</a>
				</td>
				</tr></table>
			</div>
			<?php $count++; ?>
			<?php endforeach; ?>
		</div>

		<div class="tableCommands" style="width: 410px;">
		<form action="users.php">
		<input type="hidden" name="cmd" value="create" />
		<input type="submit" value="Create New User" class="button" />
		<input type="submit" name="jumpToUserRoles" value="Manage User Roles" class="button" />
		</form>
		</div>

	<?php } ?>

<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();								   # ?>
<?php $close->write();													   # ?>
<?php ###################################################################### ?>
