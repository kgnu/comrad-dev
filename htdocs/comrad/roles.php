<?php

	require_once('initialize.php');

	// To add new permissions to the system, include them below...
	// Note: The first parameter is the permission name and the second 
	//  parameter is a default permission (please see the Permission 
	//  class for details)...
	$sysPerms = array(
		'catalog' => 'v---', 
		'changemypassword' => 'v---', 
		'djshow' => 'v---', 
		'events' => 'v---', 
		'log' => 'v---', 
		'roles' => 'v---', 
		'schedule' => 'v---', 
		'showbuilder' => 'v---', 
		'openscheduling' => '----',		// vcmr => User can schedule / unschedule events in the showbuilder anytime (cmr are aux).. otherwise they're restricted to the 2 week window
		'users' => 'v---',
		'phpmyadmin' => '----'
		);

	// Check if they clicked cancel...
	if ($uri->hasKey('cancel'))
	{
		$uri->clearKeys();
		$uri->redirect('get');
	}

	// Execute the create, modify, or remove command...
	if ($uri->getKeyAsBool('execute')) switch ($uri->getKey('cmd'))
	{
		case 'create':
			// If creating a role, we MUST have a name...
			if ($uri->getKey('name') == '') {
				$uri->removeKey('execute'); $uri->updateKey('result', 'name_empty'); $uri->redirect();
			}
			
			// TODO: Put this all in one transaction so that we can roll it back on failure
			// Insert Role
			$roleId = DB::getInstance('MySql')->insert(new Role(array(
				'Name' => $uri->getKey('name')
			)));
			
			// Insert Object Permissions
			if ($roleId) {
				foreach ($_POST['perms'] as $objectName => $operations) {
					$objects = DB::getInstance('MySql')->find(new DBObject(array('Name' => $objectName)));
					if (count($objects) > 0) {
						$object = $objects[0];
						DB::getInstance('MySql')->insert(new DBObjectPermission(array(
							'DBObjectId' => $object->Id,
							'RoleId' => $roleId,
							'Read' => array_key_exists('read', $operations) && $operations['read'] == 'on',
							'Write' => array_key_exists('write', $operations) && $operations['write'] == 'on',
							'Insert' => array_key_exists('insert', $operations) && $operations['insert'] == 'on',
							'Delete' => array_key_exists('delete', $operations) && $operations['delete'] == 'on'
						)));
					}
				}
			
				// Save log...
				$init->log('Role \'' . $uri->getKey('name') . '\' ' . 'created');

				// Redirect to success...
				$name = $uri->getKey('name');
				$uri->clearKeys();
				$uri->updateKey('cmd', 'view');
				$uri->updateKey('result', "create_success");
				$uri->updateKey('name', $name);
				$uri->redirect();
			}
			
			break;
			
		case 'modify':
		
			// If modifying a role, we MUST have a name...
			if ($uri->getKey('name') == '') {
				$uri->removeKey('execute'); $uri->updateKey('result', 'name_empty'); $uri->redirect();
			}

			// If modifying a role, we MUST have an id...
			if ($uri->getKey('id') == '')
				{ $uri->removeKey('execute'); $uri->updateKey('result', 'role_not_exist_for_modify'); $uri->redirect(); }
			
			$role = DB::getInstance('MySql')->get(new Role(array('Id' => $uri->getKey('id'))));
			
			// TODO: We can make this much, much more efficient.
			// Update this process to do updates on the existing records instead of recreating them
			
			// Clear the permissions so we can reinsert...
			$perms = DB::getInstance('MySql')->find(new DBObjectPermission(array('RoleId' => $role->Id)));
			foreach ($perms as $perm) {
				DB::getInstance('MySql')->delete($perm);
			}

			// Loop through all of the permissions and add them to the role...
			foreach ($_POST['perms'] as $objectName => $operations) {
				$objects = DB::getInstance('MySql')->find(new DBObject(array('Name' => $objectName)));
				if (count($objects) > 0) {
					$object = $objects[0];
					DB::getInstance('MySql')->insert(new DBObjectPermission(array(
						'DBObjectId' => $object->Id,
						'RoleId' => $role->Id,
						'Read' => array_key_exists('read', $operations) && $operations['read'] == 'on',
						'Write' => array_key_exists('write', $operations) && $operations['write'] == 'on',
						'Insert' => array_key_exists('insert', $operations) && $operations['insert'] == 'on',
						'Delete' => array_key_exists('delete', $operations) && $operations['delete'] == 'on'
					)));
				}
			}

			// Save parameters...
			$role->Name = $uri->getKey('name');
			DB::getInstance('MySql')->update($role);

			// Save log...
			$init->log('Role \'' . $uri->getKey('name') . '\' modified');

			// Redirect to success...
			$name = $uri->getKey('name');
			$uri->clearKeys();
			$uri->updateKey('cmd', 'view');
			$uri->updateKey('result', "modify_success");
			$uri->updateKey('name', $name);
			$uri->redirect();

			break;


		case 'remove':
			
			// If removing a role, we MUST have an id...
			if ($uri->getKey('id') == '')
				{ $uri->removeKey('execute'); $uri->updateKey('result', 'role_not_exist_for_removal'); $uri->redirect(); }
			
			$role = DB::getInstance('MySql')->get(new Role(array('Id' => $uri->getKey('id'))));
			
			// Role must exist if we're to remove it...
			if (is_null($role))
				{ $uri->removeKey('execute'); $uri->updateKey('result', 'role_not_exist_for_removal'); $uri->redirect(); }

			// Remove the role from the database...
			DB::getInstance('MySql')->delete($role);

			// Redirect to success...
			$init->log('Role \'' . $role->Name . '\' removed');

			// What do we do if there are people part of this role that we're deleting???

			// Redirect to success...
			$uri->clearKeys();
			$uri->updateKey('cmd', 'view');
			$uri->updateKey('result', 'remove_success');
			$uri->updateKey('name', $role->Name);
			$uri->redirect();

			break;
	}

	// Query string overrides the key value...
	foreach ($sysPerms as $name => $mode)
	{
		$hasMode = $uri->hasKey($name . 'View') || $uri->hasKey($name . 'Create') || 
			$uri->hasKey($name . 'Modify') || $uri->hasKey($name . 'Remove');

		$queryMode = '';
		$queryMode .= ($uri->getKey($name . 'View') == 'on') ? 'v' : '-';
		$queryMode .= ($uri->getKey($name . 'Create') == 'on') ? 'c' : '-';
		$queryMode .= ($uri->getKey($name . 'Modify') == 'on') ? 'm' : '-';
		$queryMode .= ($uri->getKey($name . 'Remove') == 'on') ? 'r' : '-';

		if ($hasMode) $sysPerms[$name] = $queryMode;
	}
?>
<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();                                   # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Role Management</title>

	<?php if (($uri->getKey('cmd') == 'create') || ($uri->getKey('cmd') == 'modify')) { ?>

	<script type="text/javascript">

		////////////////////////////////////////////////////////////////////////
		// Populate the role's permissions...
		function populatePermissions()
		{
			$('#permissions').html('');
			<?php
			echo "\n";
				foreach ($sysPerms as $name => $mode)
					echo "\t\t\taddPermission('$name', '$mode');\n";
			?>
		}

		////////////////////////////////////////////////////////////////////////
		// Add a new "permission element" to the #permissions...
		function addPermission(name, mode)
		{
			var v = mode.substr(0, 1) == 'v';
			var c = mode.substr(1, 1) == 'c';
			var m = mode.substr(2, 1) == 'm';
			var r = mode.substr(3, 1) == 'r';

			var html = '<div class="permission">' + "\n";
			html += '<span class="field">' + name + '</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + "\n";
			html += '<input type="checkbox" name="' + name + 'View" style="width: auto;" ' + (v ? 'checked="checked" ' : '') + '/> view';
			html += '&nbsp;&nbsp;&nbsp;&nbsp;' + "\n";
			html += '<input type="checkbox" name="' + name + 'Create" style="width: auto;" ' + (c ? 'checked="checked" ' : '') + '/> create';
			html += '&nbsp;&nbsp;&nbsp;&nbsp;' + "\n";
			html += '<input type="checkbox" name="' + name + 'Modify" style="width: auto;" ' + (m ? 'checked="checked" ' : '') + '/> modify';
			html += '&nbsp;&nbsp;&nbsp;&nbsp;' + "\n";
			html += '<input type="checkbox" name="' + name + 'Remove" style="width: auto;" ' + (r ? 'checked="checked" ' : '') + '/> remove' + "\n";
			html += '</div>';

			$('#permissions').append(html);
		}

		////////////////////////////////////////////////////////////////////////
		// This function checks to make sure that if a create, modify, or 
		//  remove flag has been set that it also sets the view flag...
		function checkRequiredViewFlag()
		{
			// Split at uppercase...
			var checkPair = $(this).attr('name').replace(/([A-Z])/g, ",$1").split(',');

			// If create, modify, or remove are checked, check view...
			if (checkPair[1] == 'Create' || checkPair[1] == 'Modify' || checkPair[1] == 'Remove')
				$('input[name=' + checkPair[0] + 'View]').attr('checked', true);

			// If view is UNCHECKED, then we can't have create, modify, or remove checked...
			if (checkPair[1] == 'View' && !$(checkPair[0] + 'View').is(':checked'))
			{
				$('input[name=' + checkPair[0] + 'Create]').attr('checked', false);
				$('input[name=' + checkPair[0] + 'Modify]').attr('checked', false);
				$('input[name=' + checkPair[0] + 'Remove]').attr('checked', false);
			}
		}

		////////////////////////////////////////////////////////////////////////
		$(function() {

			// populatePermissions();
			$(':checkbox').click(checkRequiredViewFlag);
			
			$('#permissions li').hover(function() {
				$(this).addClass('hover');
			}, function() {
				$(this).removeClass('hover');
			});
		});

	</script>
	
	<style type="text/css">
		div#permissions ul { list-style: none; margin: 0px; padding: 0px }
		div#permissions ul li ul { margin-left: 20px }
		div#permissions ul li { margin: 0px; padding: 0px; margin-top: 5px }
		div#permissions ul li.hover { background-color: #f8f8ff }
		div#permissions ul li ul li.hover { background-color: #f0f0ff }
		div#permissions ul li ul li ul li.hover { background-color: #e8e8ff }
		div#permissions ul li ul li ul li ul li.hover { background-color: #e0e0ff }
		div#permissions ul li div.objectName { float: left }
		div#permissions ul li div.objectOperations { margin-left: 300px }
		div#permissions label { margin-right: 30px; cursor: pointer }
		div#permissions input { cursor: pointer }
	</style>
	
	<?php } ?>

<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();                                   # ?>
<?php //$body->setPathPageIcon('media/users.png');                           # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>

	<?php
	$ret = '';
	switch ($uri->getKey('result'))
	{
		case 'name_empty':                  $ret = '<b class="error">Error:</b> The name field cannot be left blank.'; break;
		case 'role_not_exist_for_modify':   $ret = '<b class="error">Error:</b> The role you are trying to remove does not exist.'; break;
		case 'role_not_exist_for_removal':  $ret = '<b class="error">Error:</b> The role <b>' . $uri->getKey('name') . '</b> you are trying to remove does not exist.'; break;

		case 'create_success':              $ret = '<b>Success:</b> You have created the role <i>' . $uri->getKey('name') . '</i>.'; break;
		case 'modify_success':              $ret = '<b>Success:</b> You have modifed the role <i>' . $uri->getKey('name') . '</i>.'; break;
		case 'remove_success':              $ret = '<b>Success:</b> You have removed the role <i>' . $uri->getKey('name') . '</i>.'; break;
	}
	?>
	
	<?php $role = ($uri->getKey('id') ? DB::getInstance('MySql')->get(new Role(array('Id' => $uri->getKey('id')))) : null); ?>
	
	<?php if ($uri->getKey('cmd') == 'create' || $uri->getKey('cmd') == 'modify') { ?>
		<h4><?= ucwords($uri->getKey('cmd')) ?> Role</h4>

		<?php if ($ret != '') { ?>
			<div id="notice" class="<?= substr($ret, 0, 15) == '<b>Success:</b>' ? 'info' : 'error' ?>Box">
			<div class="content"><?php echo $ret; ?></div>
			</div>
		<?php } ?>

		<form name="role" action="roles.php" method="POST">
			<input type="hidden" name="cmd" value="<?= $uri->getKey('cmd') ?>" />
			<?php if ($role): ?>
			<input type="hidden" name="id" value="<?= $role->Id ?>" />
			<?php endif; ?>
			<input type="hidden" name="execute" value="1" />

			<table cellpadding="0" cellspacing="0" border="0" style="margin-left: 20px;">

			<tr>
			<td width="120"><b>Name:</b></td>
			<td><input type="text" name="name" value="<?= ($role ? $role->Name : '') ?>" style="width: 300px;" /></td>
			</tr>
			<tr><td colspan="2"><img src="media/inviso.gif" width="1" height="10" /></td></tr>

			<tr>
			<td width="120"><b>Permissions:</b></td>
			<td>
				<div id="permissions">
					<div style="float: left">
						<input id="selectAll" type="checkbox"></input> <label for="selectAll">Select All</label>
					</div>
					<div style="padding: 0px 0px 5px 300px; border-bottom: 1px solid #999">
						<input id="selectAllRead" type="checkbox"></input> <label for="selectAllRead">Read</label>
						<input id="selectAllWrite" type="checkbox"></input> <label for="selectAllWrite">Write</label>
						<input id="selectAllInsert" type="checkbox"></input> <label for="selectAllInsert">Insert</label>
						<input id="selectAllDelete" type="checkbox"></input> <label for="selectAllDelete">Delete</label>
					</div>
					<?php
						function printRecursivePermissionsList($objectParentArray, $perms = array()) {
							$out = '<ul>';
							foreach ($perms as $permName => $operations) {
								$out .= '<li><div>';
								$out .= '<div class="objectName">';
								$out .= '<input id="selectAll'.$permName.'" type="checkbox"></input> <label for="selectAll'.$permName.'">'.$permName.'</label>';
								$out .= '<script type="text/javascript">';
								$out .= "$('#selectAll".$permName."').change(function() {";
								$out .= "	if ($(this).attr('checked')) {";
								$out .= "		$('.objectOperations .".$permName."').attr('checked', 'checked').change();";
								$out .= "	} else {";
								$out .= "		$('.objectOperations .".$permName."').removeAttr('checked').change();";
								$out .= "	}";
								$out .= "});";
								$out .= "</script>";
								$out .= '</div>';
								$out .= '<div class="objectOperations">';
								$out .= '<input id="perms['.$permName.'][read]" name="perms['.$permName.'][read]" class="read '.$permName.'" type="checkbox"'.(in_array('read', $operations['operations']) ? ' checked="checked"' : '').'> <label for="perms['.$permName.'][read]">Read</label>';
								$out .= ' <input id="perms['.$permName.'][write]" name="perms['.$permName.'][write]" class="write '.$permName.'" type="checkbox"'.(in_array('write', $operations['operations']) ? ' checked="checked"' : '').'> <label for="perms['.$permName.'][write]">Write</label>';
								$out .= ' <input id="perms['.$permName.'][insert]" name="perms['.$permName.'][insert]" class="insert '.$permName.'" type="checkbox"'.(in_array('insert', $operations['operations']) ? ' checked="checked"' : '').'> <label for="perms['.$permName.'][insert]">Insert</label>';
								$out .= ' <input id="perms['.$permName.'][delete]" name="perms['.$permName.'][delete]" class="delete '.$permName.'" type="checkbox"'.(in_array('delete', $operations['operations']) ? ' checked="checked"' : '').'> <label for="perms['.$permName.'][delete]">Delete</label>';
								$out .= '</div>';
								// $out .= ($objectParentArray[$permName] != null && array_key_exists($objectParentArray[$permName], $perms) ? printRecursivePermissionsList($objectParentArray, $perms) : '');
								$out .= '</div></li>';
							}
							$out .= '</ul>';
							
							return $out;
						}
					?>
					<?= printRecursivePermissionsList(PermissionManager::getInstance()->getObjectParentArray(), $role ? PermissionManager::getInstance()->fetchPermissionsForRoleId($role->Id) : PermissionManager::getInstance()->getDefaultPermissions()) ?>
					
					<script type="text/javascript">
						$('#selectAll').change(function() {
							if ($(this).attr('checked')) {
								$('#selectAllRead, #selectAllWrite, #selectAllInsert, #selectAllDelete').attr('checked', 'checked').change();
							} else {
								$('#selectAllRead, #selectAllWrite, #selectAllInsert, #selectAllDelete').removeAttr('checked').change();
							}
						});
						$('#selectAllRead').change(function() {
							if ($(this).attr('checked')) {
								$('.objectOperations .read').attr('checked', 'checked');
							} else {
								$('.objectOperations .read').removeAttr('checked');
							}
						});
						
						$('#selectAllWrite').change(function() {
							if ($(this).attr('checked')) {
								$('.objectOperations .write').attr('checked', 'checked');
							} else {
								$('.objectOperations .write').removeAttr('checked');
							}
						});
						
						$('#selectAllInsert').change(function() {
							if ($(this).attr('checked')) {
								$('.objectOperations .insert').attr('checked', 'checked');
							} else {
								$('.objectOperations .insert').removeAttr('checked');
							}
						});
						
						$('#selectAllDelete').change(function() {
							if ($(this).attr('checked')) {
								$('.objectOperations .delete').attr('checked', 'checked');
							} else {
								$('.objectOperations .delete').removeAttr('checked');
							}
						});
					</script>
				</div>
			</td>
			</tr>
			<tr><td colspan="2"><img src="media/inviso.gif" width="1" height="10" /></td></tr>

			<tr><td width="120">&nbsp;</td><td>
			<input type="submit" value="<?= ucwords($uri->getKey('cmd')) ?> Role" class="button" onClick="javascript:this.disabled = true; document.role.submit();" />
			<input type="submit" name="cancel" value="Cancel" class="button" />
			</td></tr>

			</table>

		</form>

		<?php if ($uri->getKey('cmd') == 'create') { ?>
		<script language="javascript">
		document.role.name.focus();
		</script>
		<?php } ?>

	<?php } else if ($uri->getKey('cmd') == 'remove') { ?>

		<h4>Remove Role</h4>

		<?php if ($ret != '') { ?>
			<div id="notice" class="<?= substr($ret, 0, 15) == '<b>Success:</b>' ? 'info' : 'error' ?>Box">
			<div class="content"><?php echo $ret; ?></div>
			</div>
		<?php } ?>

		<p>Are you sure you would like to remove the <b><?= $role->Name ?></b> role?</p>

		<table cellpadding="0" cellspacing="0" border="0" style="margin-left: 20px;"><tr><td>
		<form action="roles.php" method="GET">
		<input type="hidden" name="cmd" value="remove" />
		<input type="hidden" name="execute" value="1" />
		<input type="hidden" name="id" value="<?= $uri->getKey('id') ?>" />
		<input type="submit" value="Remove Role: <?= $role->Name ?>" class="button" />
		<input type="submit" name="cancel" value="Cancel" class="button" />
		</form></td></tr></table>

	<?php } else { ?>

		<h4>Roles</h4>

		<?php if ($ret != '') { ?>
			<div id="notice" class="<?= substr($ret, 0, 15) == '<b>Success:</b>' ? 'info' : 'error' ?>Box">
			<div class="content"><?php echo $ret; ?></div>
			</div>
		<?php } ?>

		<div class="tableHeading" style="width: 410px;">Roles</div>

		<div class="tableSubheading" style="width: 410px;">
		<table cellpadding="0" cellspacing="0" border="0"><tr>
		<td width="33">&nbsp;</td>
		<td width="236"><b>Name</b></td>
		<td width="131"><b>Options...</b></td>
		</tr></table>
		</div>
        
		<?php $roles = DB::getInstance('MySql')->find(new Role()); ?>
		<?php $count = 1; ?>
		<div class="tableBody" style="width: 410px;">
			<?php foreach($roles as $role): ?>
			<div class="tableRow">
				<table cellpadding="0" cellspacing="0" border="0"><tr style="vertical-align: top;">
				<td width="33"><?= $count ?>.</td>
				<td width="236"><?= $role->Name ?></td>
				<td width="131">
					<a href="roles.php?cmd=modify&id=<?= $role->Id ?>">Modify</a>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="roles.php?cmd=remove&id=<?= $role->Id ?>">Remove</a>
				</td>
				</tr></table>
			</div>
			<?php $count++; ?>
			<?php endforeach; ?>
		</div>

		<div class="tableCommands" style="width: 410px;">
		<form action="roles.php">
		<input type="hidden" name="cmd" value="create" />
		<input type="submit" value="Create New Role" class="button" />
		</form>
		</div>

	<?php } ?>

<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();                                 # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
