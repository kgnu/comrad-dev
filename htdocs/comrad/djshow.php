<?php

	require_once('initialize.php');

	// Make sure we have view permissions...
	$init->assertPermission($userRole->getPermission('djshow')->hasView());

	// Handle module commands...
	if ($uri->getKeyAsBool('execute')) switch ($uri->getKey('cmd'))
	{
		case 'ajaxGetShows':

			$now = time();
			$startTime = strtotime($uri->getKey('startDate'));
			$endTime = strtotime($uri->getKey('endDate'));

			// We want the time range to be inclusive so we'll get the day AFTER the 
			//  selected endTime...
			$endTime = strtotime('tomorrow', $endTime);

			$events = DB::getInstance('MySql');
			$iter = new ScheduledInstanceIterator($startTime, $endTime);

			// Display message if we don't have any results...
			if ($iter->getItemCount() == 0)
			{
				die('<p>No shows have been scheduled for this time period yet. Check out the <a href="schedule.php">schedule</a> to create one.</p>');
			}

			// Loop through all of the available shows...
			while ($iter->hasNext())
			{
				$event = $iter->getNext();

				// Skip all events that are NOT shows...
				if ($event->getEventType() != 'Show') continue;

				// Get show meta data...
				$meta = $events->get(new ShowMetadata(array( 'UID' => $event->getEventID() )));

				// Calculate the mins and secs to the show...
				$mins = floor($event->getDuration() / 60.0);
				$secs = $event->getDuration() % 60;

				// Format the date and time of the event...
				$showTime = date('l, F j, Y \a\t g:ia', $event->getStartDateTime());
				if ($mins > 0 || $secs > 0) $showTime .= ' for';
				if ($mins > 0) $showTime .= " $mins minutes";
				if ($mins > 0 && $secs > 0) $showTime .= ' and';
				if ($secs > 0) $showTime .= " $secs seconds";

				// Shortcut for whether this event is live...
				$isToday = ( date('n/j/Y', $event->getStartDateTime()) == date('n/j/Y') );
				$isLive = ( $now > $event->getStartDateTime() && $now < ($event->getStartDateTime() + $event->getDuration()) );

				// Further CSS styles that we may need to apply...
				$furtherCss = '';
				if ($isToday) $furtherCss .= ' showListingToday';
				if ($isLive) $furtherCss .= ' showListingLive';

				// Print out the rest of the show details...
				echo '<div class="showListing' . $furtherCss . '"><a href="showbuilder.php?id=' . $event->getID() . '">';
				if ($isLive) echo '<span class="liveNote">Live!</span>';
				echo '<b>' . $meta->Name . '</b> (' . $showTime . ')';
				echo '</a></div>';
			}

			// Save the #from and #to values in the session...
			$_SESSION['djshow_from'] = $uri->getKey('startDate');
			$_SESSION['djshow_to'] = $uri->getKey('endDate');

			exit();
	}
	
?>
<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();                                   # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; DJ Show Information</title>

	<script type="text/javascript">
	/* <![CDATA[ */

	function populateShows(start, end, elem)
	{
		$('#btnToday').attr('disabled', '');
		$('#btnTomorrow').attr('disabled', '');
		$('#btnLastWeek').attr('disabled', '');
		$('#btnThisWeek').attr('disabled', '');
		$('#btnNextWeek').attr('disabled', '');

		if (elem != undefined) $(elem).attr('disabled', 'disabled');
		if (start != undefined) $('#from').val(start);
		if (end != undefined) $('#to').val(end);

		$('#to').datepicker('option', 'minDate', $('#from').datepicker('getDate'));

		$.post('djshow.php', { cmd: 'ajaxGetShows', execute: 1, startDate: $('#from').val(), endDate: $('#to').val() }, function (data) {
			$('#shows').html(data);
		});
	}

	$(function() {

		// Assign the jQuery datepicker control to the #from and #to textboxes...
		$('#from').datepicker({dateFormat: 'm/d/yy', showButtonPanel: true, changeMonth: false, changeYear: false});
		$('#to').datepicker({dateFormat: 'm/d/yy', showButtonPanel: true, changeMonth: false, changeYear: false});

		// Repopulate the results when the user changes either the #from or #to fields...
		$('#from, #to').change(function() { populateShows(); });

		// Populate the default date range...
		<?php if ( !isset($_SESSION['djshow_from']) && !isset($_SESSION['djshow_to']) ) { ?>
		populateShows('<?php echo date('n/j/Y'); ?>', '<?php echo date('n/j/Y', strtotime('+1 week')); ?>', '#btnThisWeek');
		<?php } else { ?>
		populateShows('<?php echo $_SESSION['djshow_from']; ?>', '<?php echo $_SESSION['djshow_to']; ?>');
		<?php } ?>

		// Handle the various shortcut buttons...
		$('#btnToday').click(function () { populateShows('<?php echo date('n/j/Y'); ?>', '<?php echo date('n/j/Y'); ?>', this); });
		$('#btnTomorrow').click(function () { populateShows('<?php echo date('n/j/Y', strtotime('tomorrow')); ?>', '<?php echo date('n/j/Y', strtotime('tomorrow')); ?>', this); });
		$('#btnLastWeek').click(function () { populateShows('<?php echo date('n/j/Y', strtotime('-1 week')); ?>', '<?php echo date('n/j/Y'); ?>', this); });
		$('#btnThisWeek').click(function () { populateShows('<?php echo date('n/j/Y'); ?>', '<?php echo date('n/j/Y', strtotime('+1 week')); ?>', this); });
		$('#btnNextWeek').click(function () { populateShows('<?php echo date('n/j/Y', strtotime('+1 week')); ?>', '<?php echo date('n/j/Y', strtotime('+2 week')); ?>', this); });

	});

	/* ]]> */
	</script>

<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();                                   # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>

	<h4>DJ Show Information</h4>

	<?php
	if ($uri->getKey('ret') == 'bad_show')
	{
		echo '<p>The show you tried to build does not exist. Please select or <a href="schedule.php">schedule</a> a new show.</p>';
	}
	?>

	<p>
	<strong>From:</strong> <input type="text" id="from" name="from" readonly="readonly" style="width: 80px;" />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<strong>To:</strong> <input type="text" id="to" name="to" readonly="readonly" style="width: 80px;" />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

	<input type="button" id="btnLastWeek" value="Last Week" />&nbsp;&nbsp;
	<input type="button" id="btnToday" value="Today" />&nbsp;&nbsp;
	<input type="button" id="btnTomorrow" value="Tomorrow" />&nbsp;&nbsp;
	<input type="button" id="btnThisWeek" value="This Week" />&nbsp;&nbsp;
	<input type="button" id="btnNextWeek" value="Next Week" />&nbsp;&nbsp;
	</p>

	<div id="shows"></div>

<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();                                 # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
