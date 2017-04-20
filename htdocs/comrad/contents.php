<?php require_once('initialize.php'); ?>
<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();                                   # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

    <title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Contents</title>

<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();                                   # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>

	<div class="columnCollection">

		<?php if (PermissionManager::getInstance()->currentUserHasPermissions('read', array('ShowEvent')) &&
			PermissionManager::getInstance()->currentUserHasPermissions(array('read', 'write', 'insert', 'delete'), 'FloatingShowElement')): ?>
		<div class="column icon">
		<a href="findshows.php">
		<img src="media/icon-dj-schedule.png" width="48" height="48" />
		<h1>Show Builder</h1>
		<p>DJ and show hosts</p>
		</a>
		</div>
		<?php endif; ?>

		<?php if (PermissionManager::getInstance()->currentUserHasPermissions('read', array('Event', 'ScheduledEvent'))): ?>
		<div class="column icon">
		<a href="calendar.php">
		<img src="media/icon-schedule.png" width="48" height="48" />
		<h1>Schedule</h1>
		<p>Manage the schedule and enter show descriptions</p>
		</a>
		</div>
		<?php endif; ?>

		<?php if (!$_SESSION['Shared']): ?>
		<div class="column icon">
		<a href="changemypassword.php">
		<img src="media/icon-change-password.png" width="48" height="48" />
		<h1>Change My Password</h1>
		<p>Change your system password</p>
		</a>
		</div>
		<?php endif; ?>

		<?php if (PermissionManager::getInstance()->currentUserHasPermissions(array('read', 'write', 'insert', 'delete'), 'User')): ?>
		<div class="darkColumn icon">
		<a href="users.php">
		<img src="media/icon-users.png" width="48" height="48" />
		<h1>Security</h1>
		<p>Manage users and access to modules</p>
		</a>
		</div>
		<?php endif; ?>

		<?php if (PermissionManager::getInstance()->currentUserHasPermissions(array('read', 'write', 'insert', 'delete'), array('Album', 'Genre', 'GenreTag', 'Track'))): ?>
		<div class="column icon">
		<a href="cake/music_library/">
		<img src="media/icon-music-management.png" width="48" height="48" />
		<h1>Manage Music Library</h1>
		<p>Manage KGNU's music library</p>
		</a>
		</div>
		<?php endif; ?>

		<?php if (PermissionManager::getInstance()->currentUserHasPermissions(array('read', 'write', 'insert', 'delete'), 'Event')): ?>
		<div class="column icon">
		<a href="events.php">
		<img src="media/icon-events.png" width="48" height="48" />
		<h1>Maintain Event Data</h1>
		<p>Add, modify, and activate/deactivate events (e.g. PSAs, Announcements, etc.)</p>
		</a>
		</div>
		<?php endif; ?>

		<div class="column">
		<a href="resources.php">
		<h1>Resources</h1>
		<p>KGNU announcements, policies, pledge drive info, and other important documents</p>
		</a>
		</div>
		
		<div class="column">
		<a href="ticketrequest.php">
		<h1>Volunteer Ticket Request</h1>
		<p>Request tickets to events</p>
		</a>
		</div>
		
		<div class="column">
		<a href="charting.php">
		<h1>Charting</h1>
		<p>Generate Charting spreadsheets</p>
		</a>
		</div>
		
		<div class="column">
		<a href="soundexchangereports.php">
		<h1>Sound Exchange Reports</h1>
		<p>Generate Sound Exchange reports</p>
		</a>
		</div>
		
		<div class="column">
		<a href="about.php">
		<h1>Help</h1>
		<p>Get help with system features</p>
		</a>
		</div>

		<?php if (false): ?>
		<div class="darkColumn icon">
		<a href="log.php">
		<img src="media/icon-log.png" width="48" height="48" />
		<h1>Activity and Debug Log</h1>
		<p>Determine who and when a user has signed on and any other status or 
		error messages observed by comrad</p>
		</a>
		</div>
		<?php endif; ?>

		<?php if (false): ?>
		<div class="darkColumn icon">
		<a href="http://kgnu.net/phpmyadmin/">
        	<img src="media/icon-database.png" width="48" height="48" />
		<h1>phpMyAdmin</h1>
		<p>Perform administrative tasks for comrads MySQL servers</p>
		</a>
		</div>
		<?php endif; ?>

	</div>

<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();                                 # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
