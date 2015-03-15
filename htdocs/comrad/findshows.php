<?php

	require_once('initialize.php');
	
?>
<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();                                   # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; DJ Show Information</title>
	
	<script type='text/javascript' src='js/date/format/date.format.js'></script>
	<script type="text/javascript" src="js/jquery/json/jquery.json.js"></script>

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
		
		$.get('ajax/geteventsbetween.php', {
			start: new Date($('#from').val()).format('yyyy-mm-dd'),
			end: new Date(new Date($('#to').val()).getTime() + 24 * 60 * 60 * 1000).format('yyyy-mm-dd'),
			types: $.toJSON([ 'Show' ])
		}, function(results) {
			results.sort(function(a, b) {
				return a.Attributes.StartDateTime - b.Attributes.StartDateTime;
			});
			
			$('#shows').empty();
			var lastDate;
			var dateContainer;
			var list;
			$.each(results, function(index, value) {
				var title = value.Attributes.ScheduledEvent.Attributes.Event.Attributes.Title;
				var startTime = new Date(value.Attributes.StartDateTime * 1000);
				var duration = value.Attributes.Duration;
				var shortDescription = (value.Attributes.ShortDescription ? value.Attributes.ShortDescription : value.Attributes.ScheduledEvent.Attributes.Event.Attributes.ShortDescription);
				var endTime = new Date(startTime.getTime() + duration * 60 * 1000);

				if (lastDate == undefined || startTime.getYear() != lastDate.getYear() || startTime.getMonth() != lastDate.getMonth() || startTime.getDate() != lastDate.getDate()) {
					$('#shows').append(dateContainer = $('<div style="border-top: 1px solid #ccc;"></div>'));
					dateContainer.append('<h5>' + startTime.format('dddd m/d/yy') + ':</h5>');
					dateContainer.append(list = $('<ul style="list-style: none"></ul>'));
				}

				list.append(
					$('<li></li>').append(
						'<strong>' + startTime.format(startTime.getMinutes() > 0 ? 'h:MMtt' : 'htt') + '</strong>' + ' to <strong>' + endTime.format(endTime.getMinutes() > 0 ? 'h:MMtt' : 'htt') + '</strong> -- '
					).append(
						$('<a href="showbuilder2.php?' + ((value.Attributes.Id) ? 'seiid=' + value.Attributes.Id : 'seid=' + value.Attributes.ScheduledEvent.Attributes.Id + '&sdt=' + value.Attributes.StartDateTime) + '"></a>').append(
							'<strong>' + title + '</strong>'
						)
					)
				);

				lastDate = startTime;
			});
		}, 'json');
	}

	$(function() {

		// Assign the jQuery datepicker control to the #from and #to textboxes...
		$('#from').datepicker({dateFormat: 'm/d/yy', showButtonPanel: true, changeMonth: false, changeYear: false});
		$('#to').datepicker({dateFormat: 'm/d/yy', showButtonPanel: true, changeMonth: false, changeYear: false});

		// Repopulate the results when the user changes either the #from or #to fields...
		$('#from, #to').change(function() { populateShows(); });

		// Populate the default date range...
		<?php if ( !isset($_SESSION['djshow_from']) && !isset($_SESSION['djshow_to']) ) { ?>
		populateShows('<?php echo date('n/j/Y'); ?>', '<?php echo date('n/j/Y', strtotime('+1 day')); ?>', '#btnThisWeek');
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
