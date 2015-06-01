<?php

	require_once('initialize.php');

	// TODO: Check permissions...
	
	$eventTypes = array(
		'Alert' => 'AlertEvent',
		'Announcement' => 'AnnouncementEvent',
		'EAS Test' => 'EASTestEvent',
		'Feature' => 'FeatureEvent',
		'Giveaway' => 'TicketGiveawayEvent',
		'Legal ID' => 'LegalIdEvent',
		'PSA' => 'PSAEvent',
		'Show' => 'ShowEvent',
		'Underwriting' => 'UnderwritingEvent'
	);
	
	$scheduledEventInstanceTypes = array(
		'Alert' => 'ScheduledAlertInstance',
		'Announcement' => 'ScheduledAnnouncementInstance',
		'EAS Test' => 'ScheduledEASTestInstance',
		'Feature' => 'ScheduledFeatureInstance',
		'Giveaway' => 'TicketGiveawayEvent',
		'Legal ID' => 'ScheduledLegalIdInstance',
		'PSA' => 'ScheduledPSAInstance',
		'Show' => 'ScheduledShowInstance',
		'Underwriting' => 'ScheduledUnderwritingInstance'
	);

?>

<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();                                   # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Schedule</title>

	<link type="text/css" rel="stylesheet" href="css/jquery/fullcalendar/fullcalendar.css" />
	<link type="text/css" rel="stylesheet" href="css/jquery/autocomplete/jquery.autocomplete.css" />
<!--	<link type="text/css" rel="stylesheet" href="css/jquery/jwysiwyg/jquery.wysiwyg.css" />-->
<!--	<link type="text/css" rel="stylesheet" href="css/jquery/jwysiwyg/jquery.wysiwyg.modal.css" />-->
	
	<script type='text/javascript' src='js/jquery/fullcalendar/fullcalendar.js'></script>
	<script type="text/javascript" src="js/jquery/qtip/jquery.qtip.js"></script>
	
	<script type='text/javascript' src='js/date/format/date.format.js'></script>
	
	<script type="text/javascript" src="js/jquery/json/jquery.json.js"></script>
	<script type="text/javascript" src="js/ajax/ajaxdbinterface.js"></script>
	<script type="text/javascript" src="js/ajax/currentuserhaspermissions.js"></script>
	
	<script type="text/javascript" src="js/jquery/ajaxqueue/jquery.ajaxqueue.js"></script>
	<script type="text/javascript" src="js/jquery/bgiframe/jquery.bgiframe.js"></script>
	<script type="text/javascript" src="js/jquery/dimensions/jquery.dimensions.js"></script>
	<script type="text/javascript" src="js/jquery/autocomplete/jquery.autocomplete.js"></script>
	
<!--	<script type="text/javascript" src="js/jquery/jwysiwyg/jquery.wysiwyg.js"></script>-->
	
	<script type="text/javascript" src="js/jquery/tinymce/tinymce/jquery.tinymce.js"></script>
	<script type="text/javascript" src="js/tinymce/kgnutinymce.js"></script>

	<script type="text/javascript">
		var params;
		var savedFcevent;
		
		$(function() {
			$('#calendar').fullCalendar({
				events: function(start, end, callback) {
					$('#loading_overlay').show();
					
					params = {
						start: start.getTime() / 1000,
						end: end.getTime() / 1000,
						fullcalendarformat: true
					};
					
					//params.partialcalendardata = true; //this is currently bugged - it creates duplicate instances if you edit an instance of a show.
					
					if ($('#calendar').fullCalendar('getView').name == 'month') params.types = $.toJSON([ 'Show' ]);
					
					$.get('ajax/geteventsbetween.php', params, function(results) {
						callback(results);
						$('#loading_overlay').hide();
					}, 'json');
				},
				header: {
					left: 'prev,next today',
					center: 'title',
					right: 'month,agendaWeek,agendaDay'
				},
				buttonText: {
					prev: '<',
					next: '>',
					month: 'Month',
					agendaWeek: 'Week',
					agendaDay: 'Day'
				},
				// viewDisplay: function() {
				// 	// TODO: Add drilldown functionality (click a header, drill into day view for that day)
				// 	// Use drillDownToDate function below
				// 	switch ($('#calendar').fullCalendar('getView').name) {
				// 		case 'month':
				// 			break;
				// 		case 'agendaWeek':
				// 			$('th.fc-sun, th.fc-mon, th.fc-tue, th.fc-wed, th.fc-thu, th.fc-fri, th.fc-sat').each(function() {
				// 				
				// 			});
				// 			
				// 			break;
				// 	}
				// },
				slotMinutes: 15,
				height: 550,
				firstHour: new Date().getHours(),
				defaultEventMinutes: 60,
				allDaySlot: false,
				defaultView: 'agendaWeek',
				editable: false,
<?php if (PermissionManager::getInstance()->currentUserHasPermissions('insert', 'ScheduledEvent')): ?>
				dayClick: function(date, allDay, jsEvent, view) {
					showNewScheduledEventDialog(date);
				},
<?php endif; ?>
				eventClick: function(fcevent, jsEvent, view) {
					if (fcevent.object == null) {
						var eventParams = params;
						eventParams.partialcalendardata = null;
						eventParams.scheduledeventid = fcevent.id
						$('#loading_overlay').show();
						savedFcevent = fcevent;
						$.get('ajax/geteventsbetween.php', eventParams, function(results) {
							$('#loading_overlay').hide();
							var fcevent = savedFcevent
							fcevent.object = results[0].object;
							<?php if (PermissionManager::getInstance()->currentUserHasPermissions('write', 'ScheduledEvent')): ?>
												if (fcevent.object.Type == 'ScheduledEventInstance') {
													showEditScheduledEventChoiceDialog(fcevent);
												} else {
													showEditScheduledEventInstanceDialog(fcevent);
												}
							<?php else: ?>
												currentUserHasPermissions($.toJSON(['write']), $.toJSON([fcevent.object.Type]), function(hasPermission) {
													if (hasPermission) {
														if (fcevent.object.Type == 'ScheduledEventInstance') {
															showNewScheduledEventInstanceDialog(fcevent);
														} else {
															showEditScheduledEventInstanceDialog(fcevent);
														}
													}
												});
							<?php endif; ?>
						}, 'json');
					} else {
					
	<?php if (PermissionManager::getInstance()->currentUserHasPermissions('write', 'ScheduledEvent')): ?>
						if (fcevent.object.Type == 'ScheduledEventInstance') {
							showEditScheduledEventChoiceDialog(fcevent);
						} else {
							showEditScheduledEventInstanceDialog(fcevent);
						}
	<?php else: ?>
						currentUserHasPermissions($.toJSON(['write']), $.toJSON([fcevent.object.Type]), function(hasPermission) {
							if (hasPermission) {
								if (fcevent.object.Type == 'ScheduledEventInstance') {
									showNewScheduledEventInstanceDialog(fcevent);
								} else {
									showEditScheduledEventInstanceDialog(fcevent);
								}
							}
						});
	<?php endif; ?>
					}
				}
			});
			
			initEditEventChoiceDialog();

			initScheduledEventDialog();
			initScheduledEventInstanceDialog();
			
			if (navigator.appName.indexOf("Internet Explorer") != -1) {
				alert("It looks like you're using Internet Explorer. The schedule will be quicker if you use Firefox, Chrome or Safari. It will still work in Internet Explorer, it'll just be slower.");
			}
		});
		
		// function drillDownToDate(date) {
		// 	$('#calendar').fullCalendar('changeView', 'agendaDay');
		// 	$('#calendar').fullCalendar('gotoDate', date);
		// }
	</script>

	<style type="text/css">
		.dialog { display: none }
		
		#loading_overlay { background-color: rgba(0, 0, 0, 0.6); width: 100%; height: 100%; margin-left: -9px; margin-top: -31px; position: fixed; z-index: 9999 }
		#loading_overlay div { color: white; font-size: 1.5em; width: 300px; position: fixed; height: 2em; left: 50%; top: 50%; margin: -0.5em 0 0 -150px; text-align: center }
		#loading_overlay div p { margin: 0 }
		
		#calendar {  }
		
		input.dateField { width: 75px }
		
/*		.fc-event { height: 0px }*/
/*		.fc-event { height: 1.8em; min-height: 1.8em }*/
		.fc-event a:hover { color: inherit; text-decoration: underline }
		
		.fc-event { font-weight: bold }
		div.fc-event.fakeInstance { font-style: italic; font-weight: normal }
		
		.AlertEvent, .fc-agenda .AlertEvent .fc-event-time, .AlertEvent a { background-color: darkred; border-color: darkred; color: white }
		.AnnouncementEvent, .fc-agenda .AnnouncementEvent .fc-event-time, .AnnouncementEvent a { background-color: green; border-color: green; color: white }
		.EASTestEvent, .fc-agenda .EASTestEvent .fc-event-time, .EASTestEvent a { background-color: #754C24; border-color: #754C24; color: white }
		.FeatureEvent, .fc-agenda .FeatureEvent .fc-event-time, .FeatureEvent a { background-color: royalblue; border-color: royalblue; color: white }
		.LegalIdEvent, .fc-agenda .LegalIdEvent .fc-event-time, .LegalIdEvent a { background-color: indigo; border-color: indigo; color: white }
		.PSAEvent, .fc-agenda .PSAEvent .fc-event-time, .PSAEvent a { background-color: darkorange; border-color: darkorange; color: white }
		.ShowEvent, .fc-agenda .ShowEvent .fc-event-time, .ShowEvent a { background-color: #007084; border-color: #007084; color: white }
		.TicketGiveawayEvent, .fc-agenda .TicketGiveawayEvent .fc-event-time, .TicketGiveawayEvent a { background-color: darkgoldenrod; border-color: darkgoldenrod; color: white }
		.UnderwritingEvent, .fc-agenda .UnderwritingEvent .fc-event-time, .UnderwritingEvent a { background-color: slategray; border-color: slategray; color: white }
		
		#Event_eventDetails, #ScheduledEvent_eventDetails, #ScheduledEventInstance_eventDetails { padding: 0px 20px; border: 1px solid #ccc; background-color: #fff; max-height: 300px }
		#Event_eventDetails, #ScheduledEventInstance_eventDetails { overflow: auto }
		#ScheduledEvent_eventDetails { height: 100px }
		
		.Event_attributes label, .ScheduledEventInstance_attributes label { display: block; font-size: 1.2em; font-weight: bold }
		.Event_attributes .field, .ScheduledEventInstance_attributes .field { width: 100%; margin: 20px 0px }
		.Event_attributes .inputField, .ScheduledEventInstance_attributes .inputField { width: 100% }
		.Event_attributes .required, .ScheduledEventInstance_attributes .required { color: #c33 }
		.Event_attributes p, .ScheduledEventInstance_attributes p { margin: 0px; padding: 0px }
	</style>

<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();                                   # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>
	
	<div id="loading_overlay">
		<div>
			<img src="media/ajax3.gif" alt="" />
			<p>Loading Events...</p>
		</div>
	</div>
	
	<div id="calendar" class="fullCalendar"></div>
	
	<div id="EditEventChoice" class="dialog">
		<script type="text/javascript">
			function initEditEventChoiceDialog() {
				// Init the dialog functionality
				$('#EditEventChoice').dialog({
					autoOpen: false,
					modal: true,
					closeOnEscape: true,
					resizable: false,
					width: 625,
					position: 'top',
					title: 'How would you like to edit this event?'
				});
			}
			
			function showEditScheduledEventChoiceDialog(fcevent) {
				$('#EditEventChoice').dialog('option', 'buttons', {
					'Edit this Instance Only': function() {
						$(this).dialog('close');
						showNewScheduledEventInstanceDialog(fcevent);
					},
					'Edit All Instances': function() {
						$(this).dialog('close');
						showEditScheduledEventDialog(fcevent);
					}
				});
				$('#EditEventChoice').dialog('open');
			}
			
			function showEditScheduledEventInstanceChoiceDialog(fcevent) {
				showEditScheduledEventInstanceDialog(fcevent);
			}
		</script>
	</div>
	
	<div id="ScheduledEvent" class="dialog">
		<script type="text/javascript">
			function initScheduledEventDialog() {
				// Init datepicker widget for date fields
				$('.dateField').datepicker({dateFormat: 'm/d/yy'});
				// $('#ScheduledEvent_startDate').datepicker({dateFormat: 'm/d/yy', showButtonPanel: true, changeMonth: false, changeYear: false});
				// $('#ScheduledEvent_endDate').datepicker({dateFormat: 'm/d/yy', showButtonPanel: true, changeMonth: false, changeYear: false});
				
				// Init the autocomplete search
				$('#ScheduledEvent_search').autocomplete('ajax/autocomplete/events.php', {
					minChars: 0,
					cacheLength: 1,
					mustMatch: true,
					matchSubset: false,
					max: 0,
					extraParams: {
						type: function() { return $('#ScheduledEvent_type').val(); }
					}
				}).result(function(jqEvent, item) {
					if (item && item.length > 1) {
						var event = $.evalJSON(item[1]);
						$('#ScheduledEvent_search').val(event.Attributes.Title);
						$('#ScheduledEvent_selectedEventId').val(event.Attributes.Id);
						
						// Calculate the End Date
						var startDate = new Date($('#ScheduledEvent_startDate').val() + ' ' + $('#ScheduledEvent_startTime').val())
						var endDate = new Date();
						var duration = (event.Type == 'ShowEvent' ? 60 : (event.Type == 'FeatureEvent' ? 10 : 1));
						endDate.setTime(startDate.getTime() + (duration * 60 * 1000));
						$('#ScheduledEvent_endDate').val(endDate.format('m/d/yyyy'));
						$('#ScheduledEvent_endTime').val(endDate.format('h:MM TT'));
						
						// Show/Hide the RecordingOffset field
						if (event.Type == 'ShowEvent') {
							$('#ScheduledEvent_recordingOffset').show();
						} else {
							$('#ScheduledEvent_recordingOffset').hide();
						}
						
						ScheduledEvent_showDetailsFor(event);
					}
				});
				
				// Init the tabs
				$('#ScheduledEvent_tabs').tabs();
				
				// Set up handler for Unschedule Event button
				$('#ScheduledEvent_removeScheduledEvent').click(function() {
					if (confirm('This will unschedule the Event at the specified time.  There is no undo.  Are you sure you want to continue?')) {
						// Build the TimeInfo
						var timeInfo = ScheduledEvent_buildTimeInfo();
						
						// Build the ScheduledEvent
						var scheduledEvent = {
							'Type': 'ScheduledEvent',
							'Attributes': {
								'Id': $('#ScheduledEvent_selectedScheduledEventId').val()
							}
						};
						
						// Delete the TimeInfo
						dbCommand('delete', timeInfo.Type, 'MySql', timeInfo.Attributes, {}, function(tiResponse) {
							if (tiResponse && !tiResponse.error) {
								// Delete the ScheduledEvent
								dbCommand('delete', scheduledEvent.Type, 'MySql', scheduledEvent.Attributes, {}, function(seResponse) {
									if(seResponse && !seResponse.error) {
										$('#calendar').fullCalendar('refetchEvents');
										$('#ScheduledEvent').dialog('close');
									}
								});
							}
						});
					}
				});

				// Init the dialog functionality
				$('#ScheduledEvent').dialog({
					autoOpen: false,
					modal: true,
					closeOnEscape: true,
					resizable: false,
					width: 600,
					position: 'top',
					buttons: {
						'Cancel': function() {
							$(this).dialog('close');
						},
						'Save': ScheduledEvent_SaveClick
					}
				});
				
				$('#ScheduledEvent_RecurrenceType').hide();
				
				$('#ScheduledEvent_Recurrence').change(function() {
					$('#ScheduledEvent_RecurrenceType').toggle();
				});
				
				$('#ScheduledEvent_DailyRecurrenceOptions').hide();
				$('#ScheduledEvent_WeeklyRecurrenceOptions').hide();
				$('#ScheduledEvent_MonthlyRecurrenceOptions').hide();
				
				$('#ScheduledEvent_RecurrenceType').change(function() {
					$('#ScheduledEvent_DailyRecurrenceOptions').hide();
					$('#ScheduledEvent_WeeklyRecurrenceOptions').hide();
					$('#ScheduledEvent_MonthlyRecurrenceOptions').hide();
					
					if ($('#ScheduledEvent_RecurrenceType').val() != '') {
						$('#ScheduledEvent_' + $('#ScheduledEvent_RecurrenceType').val() + 'RecurrenceOptions').show();
					}
				});
			}

			function showNewScheduledEventDialog(startDate) {
				// Clear the form fields
				$('#ScheduledEvent_type').val('');
				$('#ScheduledEvent_search').flushCache();
				$('#ScheduledEvent_search').val('');
				$('#ScheduledEvent_eventDetails').html('');
				$('#ScheduledEvent_selectedScheduledEventId').val('');
				$('#ScheduledEvent_selectedEventId').val('');
				$('#ScheduledEvent_selectedTimeInfoId').val('');
				$('#ScheduledEvent_recordingOffset input').val('');
				$('#ScheduledEvent_recordingOffset').hide();
				
				// Select the Event tab
				$('#ScheduledEvent_tabs').tabs('select', '#ScheduledEvent_tabs_Event');
				
				// Set the value of the date & time fields
				$('#ScheduledEvent_startDate').val(startDate.format('m/d/yyyy'));
				$('#ScheduledEvent_startTime').val(startDate.format('h:MM TT'));
				$('#ScheduledEvent_endDate').val('');
				$('#ScheduledEvent_endTime').val('');

				// Hide the Unschedule Event button
				$('#ScheduledEvent_removeScheduledEvent').hide();
				
				// Set up recurrence options
				$('#ScheduledEvent_Recurrence').removeAttr('checked');
				$('#ScheduledEvent_RecurrenceType').val('').hide();
				$('#ScheduledEvent_DailyRecurrenceOptions').hide();
				$('#ScheduledEvent_WeeklyRecurrenceOptions').hide();
				$('#ScheduledEvent_MonthlyRecurrenceOptions').hide();
				$('#ScheduledEvent_DailyInterval').val('1');
				$('#ScheduledEvent_WeeklyInterval').val('1');
				$('#ScheduledEvent_MonthlyInterval').val('1');
				$('#ScheduledEvent_DailyEndDate').val('');
				$('#ScheduledEvent_WeeklyEndDate').val('');
				$('#ScheduledEvent_MonthlyEndDate').val('');
				$('#ScheduledEvent_WeeklySunday').removeAttr('checked');
				$('#ScheduledEvent_WeeklyMonday').removeAttr('checked');
				$('#ScheduledEvent_WeeklyTuesday').removeAttr('checked');
				$('#ScheduledEvent_WeeklyWednesday').removeAttr('checked');
				$('#ScheduledEvent_WeeklyThursday').removeAttr('checked');
				$('#ScheduledEvent_WeeklyFriday').removeAttr('checked');
				$('#ScheduledEvent_WeeklySaturday').removeAttr('checked');

				// Open the dialog
				$('#ScheduledEvent').dialog('option', 'title', 'Schedule an Event');
				$('#ScheduledEvent').dialog('open');

				// Set the focus to the search field
				$('#ScheduledEvent_type').focus();
				
			}
			
			function showEditScheduledEventDialog(fcevent) {
				var scheduledEventInstance = fcevent.object;
				var scheduledEvent = scheduledEventInstance.Attributes.ScheduledEvent;
				var event = scheduledEvent.Attributes.Event;
				var timeInfo = scheduledEvent.Attributes.TimeInfo;
				
				// Calculate the End Date
				var startDate = new Date(timeInfo.Attributes.StartDateTime * 1000);
				var endDate = new Date();
				endDate.setTime(startDate.getTime() + (timeInfo.Attributes.Duration * 60 * 1000));
				
				// Set the form fields
				$('#ScheduledEvent_type').val(event.Type);
				$('#ScheduledEvent_search').flushCache();
				$('#ScheduledEvent_search').val(event.Attributes.Title);
				$('#ScheduledEvent_selectedScheduledEventId').val(scheduledEvent.Attributes.Id);
				$('#ScheduledEvent_selectedEventId').val(event.Attributes.Id);
				$('#ScheduledEvent_selectedTimeInfoId').val(timeInfo.Attributes.Id);
				$('#ScheduledEvent_recordingOffset input').val(scheduledEvent.Attributes.RecordingOffset);

				// Select the Event tab
				$('#ScheduledEvent_tabs').tabs('select', '#ScheduledEvent_tabs_Event');
				
				// Show/Hide the RecordingOffset field
				if (event.Type == 'ShowEvent') {
					$('#ScheduledEvent_recordingOffset').show();
				} else {
					$('#ScheduledEvent_recordingOffset').hide();
				}
				
				// Set the value of the date & time fields
				$('#ScheduledEvent_startDate').val(startDate.format('m/d/yyyy'));
				$('#ScheduledEvent_startTime').val(startDate.format('h:MM TT'));
				$('#ScheduledEvent_endDate').val(endDate.format('m/d/yyyy'));
				$('#ScheduledEvent_endTime').val(endDate.format('h:MM TT'));
				
				// Set the recurrence fields
				if (timeInfo.Type == 'NonRepeatingTimeInfo') {
					$('#ScheduledEvent_Recurrence').removeAttr('checked');
					$('#ScheduledEvent_RecurrenceType').hide();
					
					$('#ScheduledEvent_DailyRecurrenceOptions').hide();
					$('#ScheduledEvent_WeeklyRecurrenceOptions').hide();
					$('#ScheduledEvent_MonthlyRecurrenceOptions').hide();
				} else {
					var recurrenceType = timeInfo.Type.substring(0, timeInfo.Type.length - 17); // Remove the "RepeatingTimeInfo" from the end
					var recurrenceEndDate = new Date(timeInfo.Attributes.EndDate * 1000);
					
					$('#ScheduledEvent_Recurrence').attr('checked', 'checked');
					$('#ScheduledEvent_RecurrenceType').val(recurrenceType).show();
					
					$('#ScheduledEvent_DailyRecurrenceOptions').hide();
					$('#ScheduledEvent_WeeklyRecurrenceOptions').hide();
					$('#ScheduledEvent_MonthlyRecurrenceOptions').hide();
					$('#ScheduledEvent_' + recurrenceType + 'RecurrenceOptions').show();
					
					$('#ScheduledEvent_' + recurrenceType + 'Interval').val(timeInfo.Attributes.Interval);
					if (timeInfo.Attributes.EndDate && timeInfo.Attributes.EndDate > 0) {
						$('#ScheduledEvent_' + recurrenceType + 'EndDate').val(recurrenceEndDate.format('m/d/yyyy'));
					} else {
						$('#ScheduledEvent_' + recurrenceType + 'EndDate').val('');
					}
					
					if (recurrenceType = 'Weekly') {
						(timeInfo.Attributes.WeeklyOnSunday ? $('#ScheduledEvent_WeeklySunday').attr('checked', 'checked') : $('#ScheduledEvent_WeeklySunday').removeAttr('checked'));
						(timeInfo.Attributes.WeeklyOnMonday ? $('#ScheduledEvent_WeeklyMonday').attr('checked', 'checked') : $('#ScheduledEvent_WeeklyMonday').removeAttr('checked'));
						(timeInfo.Attributes.WeeklyOnTuesday ? $('#ScheduledEvent_WeeklyTuesday').attr('checked', 'checked') : $('#ScheduledEvent_WeeklyTuesday').removeAttr('checked'));
						(timeInfo.Attributes.WeeklyOnWednesday ? $('#ScheduledEvent_WeeklyWednesday').attr('checked', 'checked') : $('#ScheduledEvent_WeeklyWednesday').removeAttr('checked'));
						(timeInfo.Attributes.WeeklyOnThursday ? $('#ScheduledEvent_WeeklyThursday').attr('checked', 'checked') : $('#ScheduledEvent_WeeklyThursday').removeAttr('checked'));
						(timeInfo.Attributes.WeeklyOnFriday ? $('#ScheduledEvent_WeeklyFriday').attr('checked', 'checked') : $('#ScheduledEvent_WeeklyFriday').removeAttr('checked'));
						(timeInfo.Attributes.WeeklyOnSaturday ? $('#ScheduledEvent_WeeklySaturday').attr('checked', 'checked') : $('#ScheduledEvent_WeeklySaturday').removeAttr('checked'));
					}
				}

				// Show the Unschedule Event button
				$('#ScheduledEvent_removeScheduledEvent').show();

				// Open the dialog
				$('#ScheduledEvent').dialog('option', 'title', 'Edit All Instances of Event - ' + event.Attributes.Title);
				$('#ScheduledEvent').dialog('open');

				// Show the Event Details
				ScheduledEvent_showDetailsFor(fcevent.object.Attributes.ScheduledEvent.Attributes.Event);
			}

			function ScheduledEvent_showDetailsFor(event) {
				$('#ScheduledEvent_eventDetails').html(
					$('<h1>' + event.Attributes.Title + '</h1><p>More details will be implemented here...</p>')
				);
				// TODO: Implement more details for each event type
			}

			function ScheduledEvent_buildTimeInfo() {
				var startDateTime = new Date($('#ScheduledEvent_startDate').val() + ' ' + $('#ScheduledEvent_startTime').val());
				var endDateTime = new Date($('#ScheduledEvent_endDate').val() + ' ' + $('#ScheduledEvent_endTime').val());

				var timeInfo = {
					'Type': 'NonRepeatingTimeInfo',
					'Attributes': {
						'StartDateTime': startDateTime.format('yyyy-mm-dd HH:MM:ss'),
						'Duration': (endDateTime.getTime() - startDateTime.getTime()) / 60 / 1000
					}
				};
				
				if ($('#ScheduledEvent_Recurrence').attr('checked') && $('#ScheduledEvent_RecurrenceType').val() != '') {
					timeInfo.Type = $('#ScheduledEvent_RecurrenceType').val() + 'RepeatingTimeInfo';
					timeInfo.Attributes.Interval = $('#ScheduledEvent_' + $('#ScheduledEvent_RecurrenceType').val() + 'Interval').val();
					if (timeInfo.Attributes.Interval == '' || timeInfo.Attributes.Interval <= 0) delete(timeInfo.Attributes.Interval);
					if ($('#ScheduledEvent_' + $('#ScheduledEvent_RecurrenceType').val() + 'EndDate').val() != '') {
						timeInfo.Attributes.EndDate = $('#ScheduledEvent_' + $('#ScheduledEvent_RecurrenceType').val() + 'EndDate').val();
					}
					
					if ($('#ScheduledEvent_RecurrenceType').val() == 'Weekly') {
						timeInfo.Attributes.WeeklyOnSunday = $('#ScheduledEvent_WeeklySunday').attr('checked');
						timeInfo.Attributes.WeeklyOnMonday = $('#ScheduledEvent_WeeklyMonday').attr('checked');
						timeInfo.Attributes.WeeklyOnTuesday = $('#ScheduledEvent_WeeklyTuesday').attr('checked');
						timeInfo.Attributes.WeeklyOnWednesday = $('#ScheduledEvent_WeeklyWednesday').attr('checked');
						timeInfo.Attributes.WeeklyOnThursday = $('#ScheduledEvent_WeeklyThursday').attr('checked');
						timeInfo.Attributes.WeeklyOnFriday = $('#ScheduledEvent_WeeklyFriday').attr('checked');
						timeInfo.Attributes.WeeklyOnSaturday = $('#ScheduledEvent_WeeklySaturday').attr('checked');
					} else if ($('#ScheduledEvent_RecurrenceType').val() == 'Monthly') {
						
					}
				}
				
				if (parseInt($('#ScheduledEvent_selectedTimeInfoId').val()) > 0) {
					timeInfo.Attributes.Id = $('#ScheduledEvent_selectedTimeInfoId').val();
				}

				return timeInfo;
			}

			function ScheduledEvent_SaveClick() {
				var timeInfo = ScheduledEvent_buildTimeInfo();
				
				if ($('#ScheduledEvent_selectedEventId').val() == '') {
					alert('You have to pick an event to schedule first!');
					$('#ScheduledEvent_search').focus();
					return;
				}
				
				// Save the TimeInfo
				dbCommand('save', timeInfo.Type, 'MySql', timeInfo.Attributes, {}, function(savedTimeInfo) {
					if (savedTimeInfo && !savedTimeInfo.error) {
						// Build the ScheduledEvent
						var scheduledEvent = {
							'Type': 'ScheduledEvent',
							'Attributes': {
								'EventId': $('#ScheduledEvent_selectedEventId').val(),
								'TimeInfoId': savedTimeInfo.Id
							}
						};
						
						if ($('#ScheduledEvent_recordingOffset').is(':visible')) {
							scheduledEvent.Attributes.RecordingOffset = $('#ScheduledEvent_recordingOffset input').val()
						}
						
						if (parseInt($('#ScheduledEvent_selectedScheduledEventId').val()) > 0) {
							scheduledEvent.Attributes.Id = $('#ScheduledEvent_selectedScheduledEventId').val();
						}
						
						// Save the ScheduledEvent
						dbCommand('save', scheduledEvent.Type, 'MySql', scheduledEvent.Attributes, {}, function(savedScheduledEvent) {
							if (savedScheduledEvent && !savedScheduledEvent.error) {
								$('#calendar').fullCalendar('refetchEvents');
								$('#ScheduledEvent').dialog('close');
							}
						});
					}
				});
			}
		</script>
		<div style="text-align: right">
			<button id="ScheduledEvent_removeScheduledEvent">Remove all instances of this event</button>
		</div>
		<div id="ScheduledEvent_tabs">
			<ul>
				<li><a href="#ScheduledEvent_tabs_Event">Event</a></li>
				<li><a href="#ScheduledEvent_tabs_Time">Time</a></li>
			</ul>
			<div id="ScheduledEvent_tabs_Event">
				<div>
					<label for="ScheduledEvent_type">Event: </label>
					<select id="ScheduledEvent_type" onchange="$('#ScheduledEvent_search').val(''); $('#ScheduledEvent_search').flushCache(); $('#ScheduledEvent_search').focus();">
						<?php foreach($eventTypes as $eventLabel => $eventValue): ?>
							<option value="<?php echo $eventValue ?>"><?php echo $eventLabel ?></option>
						<?php endforeach; ?>
					</select>
					<input id="ScheduledEvent_search" type="text"></input>
					<span id="loading"></span>
					<div id="ScheduledEvent_eventDetails">

					</div>
				</div>
				<input id="ScheduledEvent_selectedEventId" type="hidden"></input>
			</div>
			<div id="ScheduledEvent_tabs_Time">
				<div id="ScheduledEvent_recordingOffset" style="float: right; text-align: right">
					Delay recording for <input type="text" style="width: 30px" value="0"></input> minutes
				</div>
				<table>
					<tr>
						<td style="text-align: right">
							<label for="ScheduledEvent_startDate">Start Time:</label>
						</td>
						<td>
							<input id="ScheduledEvent_startDate" type="text" class="dateField"></input>
							<input id="ScheduledEvent_startTime" type="text" style="width: 65px"></input>
						</td>
					</tr>
					<tr>
						<td style="text-align: right">
							<label for="ScheduledEvent_endDate">End Time:</label>
						</td>
						<td>
							<input id="ScheduledEvent_endDate" type="text" class="dateField"></input>
							<input id="ScheduledEvent_endTime" type="text" style="width: 65px"></input>
						</td>
					</tr>
				</table>
				<fieldset style="margin-top: 10px; background-color: #fff; border: 1px solid #ccc">
					<legend>
						<input id="ScheduledEvent_Recurrence" type="checkbox"> <label for="ScheduledEvent_Recurrence">Repeating</label>
						<select id="ScheduledEvent_RecurrenceType">
							<option value="">Select One...</option>
							<option value="Daily">Daily</option>
							<option value="Weekly">Weekly</option>
							<option value="Monthly">Monthly</option>
						</select>
					</legend>
					<div id="ScheduledEvent_DailyRecurrenceOptions">
						<div>Repeats every <input id="ScheduledEvent_DailyInterval" type="text" style="width: 30px" maxlength="2"> days.</div>
						<div>Until <input id="ScheduledEvent_DailyEndDate" type="text" class="dateField"></div>
					</div>
					<div id="ScheduledEvent_WeeklyRecurrenceOptions">
						<div>Repeats every <input id="ScheduledEvent_WeeklyInterval" type="text" style="width: 30px" maxlength="2"> weeks on:</div>
						<div>
							<input id="ScheduledEvent_WeeklySunday" type="checkbox"> <label for="ScheduledEvent_WeeklySunday">Sun</label>
							<input id="ScheduledEvent_WeeklyMonday" type="checkbox"> <label for="ScheduledEvent_WeeklyMonday">Mon</label>
							<input id="ScheduledEvent_WeeklyTuesday" type="checkbox"> <label for="ScheduledEvent_WeeklyTuesday">Tues</label>
							<input id="ScheduledEvent_WeeklyWednesday" type="checkbox"> <label for="ScheduledEvent_WeeklyWednesday">Wed</label>
							<input id="ScheduledEvent_WeeklyThursday" type="checkbox"> <label for="ScheduledEvent_WeeklyThursday">Thurs</label>
							<input id="ScheduledEvent_WeeklyFriday" type="checkbox"> <label for="ScheduledEvent_WeeklyFriday">Fri</label>
							<input id="ScheduledEvent_WeeklySaturday" type="checkbox"> <label for="ScheduledEvent_WeeklySaturday">Sat</label>
						</div>
						<div>Until <input id="ScheduledEvent_WeeklyEndDate" type="text" class="dateField"></div>
					</div>
					<div id="ScheduledEvent_MonthlyRecurrenceOptions">
						<div>Repeats every <input id="ScheduledEvent_MonthlyInterval" type="text" style="width: 30px" maxlength="2"> months on:</div>
						<select id="ScheduledEvent_MonthlyRepeatType">
							<option>The 12th day of the month.</option>
							<option>The 18th to last day of the month.</option>
							<option>Wednesday of the second week of the month.</option>
							<option>Wednesday of the third to last week of the month.</option>
						</select>
						<div>Until <input id="ScheduledEvent_MonthlyEndDate" type="text" class="dateField"></div>
					</div>
				</fieldset>
				<input id="ScheduledEvent_selectedTimeInfoId" type="hidden"></input>
			</div>
		</div>
		<input id="ScheduledEvent_selectedScheduledEventId" type="hidden"></input>
	</div>
	
	<div id="ScheduledEventInstance" class="dialog">
		<script type="text/javascript">
			function initScheduledEventInstanceDialog() {
				// Init datepicker widget for date fields
				$('#ScheduledEventInstance_startDate').datepicker({dateFormat: 'm/d/yy', showButtonPanel: true, changeMonth: false, changeYear: false});
				$('#ScheduledEventInstance_endDate').datepicker({dateFormat: 'm/d/yy', showButtonPanel: true, changeMonth: false, changeYear: false});
				
				// Init the dialog functionality
				$('#ScheduledEventInstance').dialog({
					autoOpen: false,
					modal: true,
					closeOnEscape: true,
					resizable: false,
					width: 600,
					position: 'top',
					open: function(event, id) {
						initRichTextFieldsFor($('#ScheduledEventInstance'));
					},
					beforeclose: function(event, ui) {
						if (kgnuTinyMCEInitialized) removeRichTextFieldsFor($('#ScheduledEventInstance'));
					},
					buttons: {
						'Cancel': function() {
							$(this).dialog('close');
						},
						'Save': ScheduledEventInstance_SaveClick
					}
				});
				
				// Init the tabs
				$('#ScheduledEventInstance_tabs').tabs();
				
				// Set up handler Add Scheduled Event Exception button
				$('#ScheduledEvent_addScheduledEventException').click(function() {
					if (confirm('This will create an exception in the event for this day.  Are you sure you want to continue?')) {
						var exceptionAttributes = {
							ScheduledEventId: parseInt($('#ScheduledEventInstance_selectedScheduledEventId').val()),
							ExceptionDate: (new Date($('#ScheduledEventInstance_startDate').val())).format('yyyy-mm-dd')
						}
						
						dbCommand('insert', 'ScheduledEventException', 'MySql', exceptionAttributes, {}, function(seeResponse) {
							if(seeResponse && !seeResponse.error) {
								$('#calendar').fullCalendar('refetchEvents');
								$('#ScheduledEventInstance').dialog('close');
							}
						});
					}
				});
				
				// Set up handler for Unschedule Event button
				$('#ScheduledEventInstance_removeScheduledEventInstance_button').click(function() {
					if (confirm('This will remove the changes made to this instance.  There is no undo.  Are you sure you want to continue?')) {
						// Build the ScheduledEventInstance
						var scheduledEventInstance = ScheduledEventInstance_buildScheduledEventInstance();
	
						// Delete the ScheduledEventInstance
						dbCommand('delete', scheduledEventInstance.Type, 'MySql', scheduledEventInstance.Attributes, {}, function(seiResponse) {
							if(seiResponse && !seiResponse.error) {
								$('#calendar').fullCalendar('refetchEvents');
								$('#ScheduledEventInstance').dialog('close');
							}
						});
					}
				});
			}
			
			function removeRichTextFieldsFor(element) {
				$.each($(':tinymce', element), function(i, textarea) {
					tinyMCE.execCommand('mceRemoveControl', false, $(textarea).attr('id'));
				});
			}
			
			function initRichTextFieldsFor(element) {
				// Init TinyMCE fields
				initializeKGNUTinyMCEForSelector($('.tinymce:visible', element));
			}
			
			function showNewScheduledEventInstanceDialog(fcevent) {
				var scheduledEventInstance = fcevent.object;
				var scheduledEvent = scheduledEventInstance.Attributes.ScheduledEvent;
				var event = scheduledEvent.Attributes.Event;
				var timeInfo = scheduledEvent.Attributes.TimeInfo;

				// Calculate the usual Start and End Date
				var startDate = new Date(scheduledEventInstance.Attributes.StartDateTime * 1000);
				var endDate = new Date();
				endDate.setTime(startDate.getTime() + (timeInfo.Attributes.Duration * 60 * 1000));
				
				// Set the fields
				$('#ScheduledEventInstance_eventTitle').text(event.Attributes.Title);
				$('#ScheduledEventInstance_selectedScheduledEventInstanceId').val('');
				$('#ScheduledEventInstance_selectedScheduledEventInstanceType').val('Scheduled' + event.Type.substr(0, event.Type.length - 5) + 'Instance');
				$('#ScheduledEventInstance_selectedScheduledEventId').val(scheduledEvent.Attributes.Id);

				// Select the Event tab
				$('#ScheduledEventInstance_tabs').tabs('select', '#ScheduledEventInstance_tabs_Event');
				
				// Set the value of the date & time fields
				$('#ScheduledEventInstance_startDate').val(startDate.format('m/d/yyyy'));
				$('#ScheduledEventInstance_startTime').val(startDate.format('h:MM TT'));
				$('#ScheduledEventInstance_endDate').val(endDate.format('m/d/yyyy'));
				$('#ScheduledEventInstance_endTime').val(endDate.format('h:MM TT'));
				
				// Hide the Revert Changes button
				$('#ScheduledEventInstance_removeScheduledEventInstance').hide();
				
				// Show the Add Exception button
				$('#ScheduledEvent_addScheduledEventException').show();
				
				// Show the Event Details
				ScheduledEventInstance_showDetailsFor(scheduledEventInstance);
				
				currentUserHasPermissions($.toJSON(['write']), $.toJSON(['TimeInfo']), function(hasPermission) {
					if (hasPermission) {
						$('#ScheduledEventInstance_startTime').removeAttr('disabled');
						$('#ScheduledEventInstance_endTime').removeAttr('disabled');
					} else {
						$('#ScheduledEventInstance_startTime').attr('disabled', 'disabled');
						$('#ScheduledEventInstance_endTime').attr('disabled', 'disabled');
					}
					
					// Open the dialog
					$('#ScheduledEventInstance').dialog('option', 'title', 'Edit ' + event.Attributes.Title + ' for ' + startDate.format('m/d/yyyy'));
					$('#ScheduledEventInstance').dialog('open');
				});
			}
			
			function showEditScheduledEventInstanceDialog(fcevent) {
				var scheduledEventInstance = fcevent.object;
				var scheduledEvent = scheduledEventInstance.Attributes.ScheduledEvent;
				var event = scheduledEvent.Attributes.Event;
				var timeInfo = scheduledEvent.Attributes.TimeInfo;
				
				// Calculate the Instance's Start and End Date
				var startDate = new Date(scheduledEventInstance.Attributes.StartDateTime * 1000);
				var endDate = new Date();
				endDate.setTime(startDate.getTime() + (scheduledEventInstance.Attributes.Duration * 60 * 1000));
				
				// Calculate the usual Start and End Date
				var usualStartDate = new Date(timeInfo.Attributes.StartDateTime * 1000);
				var usualEndDate = new Date();
				usualEndDate.setTime(usualStartDate.getTime() + (timeInfo.Attributes.Duration * 60 * 1000));
				
				// Set the fields
				$('#ScheduledEventInstance_eventTitle').text(event.Attributes.Title);
				$('#ScheduledEventInstance_selectedScheduledEventInstanceId').val(scheduledEventInstance.Attributes.Id);
				$('#ScheduledEventInstance_selectedScheduledEventInstanceType').val(scheduledEventInstance.Type);
				$('#ScheduledEventInstance_selectedScheduledEventId').val(scheduledEventInstance.Attributes.ScheduledEventId);
				
				// Select the Event tab
				$('#ScheduledEventInstance_tabs').tabs('select', '#ScheduledEventInstance_tabs_Event');
				
				// Set the value of the date & time fields
				$('#ScheduledEventInstance_startDate').val(startDate.format('m/d/yyyy'));
				$('#ScheduledEventInstance_startTime').val(startDate.format('h:MM TT'));
				$('#ScheduledEventInstance_endDate').val(endDate.format('m/d/yyyy'));
				$('#ScheduledEventInstance_endTime').val(endDate.format('h:MM TT'));
				
				// Show the Revert Changes button if the user has permissions
				$('#ScheduledEventInstance_removeScheduledEventInstance').hide();
				currentUserHasPermissions($.toJSON(['delete']), $.toJSON([$('#ScheduledEventInstance_selectedScheduledEventInstanceType').val()]), function(hasPermission) {
					if (hasPermission) $('#ScheduledEventInstance_removeScheduledEventInstance').show();
				});
				
				// Hide the Add Exception button
				$('#ScheduledEvent_addScheduledEventException').hide();
				
				// Show the Event Details
				ScheduledEventInstance_showDetailsFor(scheduledEventInstance);
				
				currentUserHasPermissions($.toJSON(['write']), $.toJSON(['TimeInfo']), function(hasPermission) {
					if (hasPermission) {
						$('#ScheduledEventInstance_startTime').removeAttr('disabled');
						$('#ScheduledEventInstance_endTime').removeAttr('disabled');
					} else {
						$('#ScheduledEventInstance_startTime').attr('disabled', 'disabled');
						$('#ScheduledEventInstance_endTime').attr('disabled', 'disabled');
					}
					
					// Open the dialog
					$('#ScheduledEventInstance').dialog('option', 'title', 'Edit Event Instance - ' + event.Attributes.Title + ' - ' + startDate.format('m/d/yyyy'));
					$('#ScheduledEventInstance').dialog('open');
				});
			}
			
			function ScheduledEventInstance_SaveClick() {
				var scheduledEventInstance = ScheduledEventInstance_buildScheduledEventInstance();
				
				// Save the TimeInfo
				dbCommand('save', scheduledEventInstance.Type, 'MySql', scheduledEventInstance.Attributes, {}, function(savedScheduledEventInstance) {
					if (savedScheduledEventInstance && !savedScheduledEventInstance.error) {
						$('#calendar').fullCalendar('refetchEvents');
						$('#ScheduledEventInstance').dialog('close');
					}
				});
			}

			function ScheduledEventInstance_buildScheduledEventInstance() {
				var startDateTime = new Date($('#ScheduledEventInstance_startDate').val() + ' ' + $('#ScheduledEventInstance_startTime').val());
				var endDateTime = new Date($('#ScheduledEventInstance_endDate').val() + ' ' + $('#ScheduledEventInstance_endTime').val());

				var scheduledEventInstance = {
					'Type': $('#ScheduledEventInstance_selectedScheduledEventInstanceType').val(),
					'Attributes': {
						'StartDateTime': startDateTime.format('yyyy-mm-dd HH:MM:ss'),
						'Duration': (endDateTime.getTime() - startDateTime.getTime()) / 60 / 1000
					}
				};

				if (parseInt($('#ScheduledEventInstance_selectedScheduledEventId').val()) > 0) {
					scheduledEventInstance.Attributes.ScheduledEventId = $('#ScheduledEventInstance_selectedScheduledEventId').val();
				}

				if (parseInt($('#ScheduledEventInstance_selectedScheduledEventInstanceId').val()) > 0) {
					scheduledEventInstance.Attributes.Id = $('#ScheduledEventInstance_selectedScheduledEventInstanceId').val();
				}
				
				$(':input', '#ScheduledEventInstance_attributesFor' + scheduledEventInstance.Type).each(function() {
					if (this.name.substr(0, ('ScheduledEventInstance_' + scheduledEventInstance.Type).length) == ('ScheduledEventInstance_' + scheduledEventInstance.Type)) {
						var inputType = this.type;
						var inputTag = this.tagName.toLowerCase();
						if (inputType == 'text' || inputType == 'password' || inputType == 'hidden') {
							scheduledEventInstance.Attributes[this.id.substr(('ScheduledEventInstance_' + scheduledEventInstance.Type).length)] = this.value;
						} else if (inputType == 'checkbox') {
							scheduledEventInstance.Attributes[this.id.substr(('ScheduledEventInstance_' + scheduledEventInstance.Type).length)] = this.checked;
						} else if (inputType == 'radio') {
							if (this.checked) scheduledEventInstance.Attributes[this.id.substr(('ScheduledEventInstance_' + scheduledEventInstance.Type).length)] = this.value;
						} else if (inputTag == 'textarea' || inputTag == 'select') {
							scheduledEventInstance.Attributes[this.id.substr(('ScheduledEventInstance_' + scheduledEventInstance.Type).length)] = $(this).val();
						}
					}
				});

				return scheduledEventInstance;
			}

			function ScheduledEventInstance_showDetailsFor(scheduledEventInstance) {
				var event = scheduledEventInstance.Attributes.ScheduledEvent.Attributes.Event;
				var type;
				
				if (scheduledEventInstance.Type == 'ScheduledEventInstance') {
					type = 'Scheduled' + event.Type.substr(0, event.Type.length - ('Event').length) + 'Instance';
				} else {
					type = scheduledEventInstance.Type;
				}
				
				$(':input', '#ScheduledEventInstance_attributesFor' + type).each(function() {
					var attributeName = this.name.substr(('ScheduledEventInstance_' + type).length);
					var attributeValue;
					if (scheduledEventInstance.Attributes[attributeName]) {
						attributeValue = scheduledEventInstance.Attributes[attributeName];
					} else if (event.Attributes[attributeName]) {
						attributeValue = event.Attributes[attributeName];
					} else {
						attributeValue = '';
					}
					
					var elementType = this.type;
					var elementTag = this.tagName.toLowerCase();
					if ($(this).hasClass('datePickerInput')) {
						var d = new Date(attributeValue * 1000);
						this.value = (d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear();
					} else if (elementType == 'text' || elementType == 'password' || elementType == 'hidden') {
						this.value = attributeValue;
					} else if (elementType == 'checkbox') {
						this.checked = attributeValue;
					} else if (elementType == 'radio') {
						$('#ScheduledEventInstance_' + type + attributeName + '[value="' + attributeValue + '"]').attr('checked', true);
					} else if (elementTag == 'textarea' || elementTag == 'select') {
						$(this).val(attributeValue);
					}
				});

				$('.ScheduledEventInstance_attributes').hide();
				$('#ScheduledEventInstance_attributesFor' + type).show();
				ScheduledEventInstance_initAttributeFields(type);
			}

			function ScheduledEventInstance_initAttributeFields(type) {
				$('#ScheduledEventInstance_attributesFor' + type + ' .datePickerInput').datepicker({dateFormat: 'm/d/yy', showButtonPanel: true, changeMonth: true, changeYear: true});

				// Init rich-text editor too!
			}
		</script>
		
<?php if (PermissionManager::getInstance()->currentUserHasPermissions('insert', 'ScheduledEventException')): ?>
		<div style="text-align: right">
			<button id="ScheduledEvent_addScheduledEventException">Remove this instance</button>
		</div>
<?php endif; ?>
		<div id="ScheduledEventInstance_tabs">
			<ul>
				<li><a href="#ScheduledEventInstance_tabs_Event">Event</a></li>
				<li><a href="#ScheduledEventInstance_tabs_Time">Time</a></li>
			</ul>
			<div id="ScheduledEventInstance_tabs_Event">
				<div>
					<h2 id="ScheduledEventInstance_eventTitle"></h2>
					<div id="ScheduledEventInstance_eventDetails">
						<?php foreach ($scheduledEventInstanceTypes as $eventLabel => $scheduledEventInstanceType): ?>
							<?php $scheduledEventInstance = new $scheduledEventInstanceType(); ?>
							<div id="ScheduledEventInstance_attributesFor<?php echo $scheduledEventInstanceType ?>" class="ScheduledEventInstance_attributes" style="display: none">
								<?php foreach ($scheduledEventInstance->getColumns() as $columnName => $column): ?>
									<?php if (!array_key_exists('showinform', $column) || (array_key_exists('showinform', $column) && $column['showinform'] == false)) continue; ?>
									<div class="field">
										<label for="ScheduledEventInstance_<?php echo $scheduledEventInstanceType.$columnName ?>"><?php echo (array_key_exists('tostring', $column) ? $column['tostring'] : $columnName) ?><?php if ($scheduledEventInstance->isRequiredField($columnName) && $column['type'] != 'Boolean') echo '<span class="required">*</span>' ?></label>
										<?php switch($column['type']) {

					            			case 'ForeignKey': ?>
            									<?php $foreignObject = new $column['foreignType'](); $optionObjects = DB::getInstance('MySql')->find($foreignObject, $count, array('sortcolumn' => $foreignObject->getTitleColumn(), 'limit' => false)); ?>
            									<select id="ScheduledEventInstance_<?php echo $scheduledEventInstanceType.$columnName ?>" name="ScheduledEventInstance_<?php echo $scheduledEventInstanceType.$columnName ?>" class="inputField">
            									    <option value="">None</option>
            										<?php foreach ($optionObjects as $object): ?>
            											<option value="<?php echo $object->{$object->getPrimaryKey()} ?>"><?php echo $object->{$object->getTitleColumn()} ?></option>
            										<?php endforeach; ?>
            									</select>
            								<?php break;
									
											case 'String': ?>
												<textarea name="ScheduledEventInstance_<?php echo $scheduledEventInstanceType.$columnName ?>" id="ScheduledEventInstance_<?php echo $scheduledEventInstanceType.$columnName ?>" class="inputField tinymce" disabled="disabled"><?php if(($default = $scheduledEventInstance->getColumnDefault($columnName)) != null) echo $default ?></textarea>
											<?php break;
									
											case 'ShortString':
											case 'UppercaseString': ?>
												<input type="text" name="ScheduledEventInstance_<?php echo $scheduledEventInstanceType.$columnName ?>" id="ScheduledEventInstance_<?php echo $scheduledEventInstanceType.$columnName ?>" class="inputField"<?php if(($default = $scheduledEventInstance->getColumnDefault($columnName)) != null) echo ' value="'.$default.'"' ?>>
											<?php break;
									
											case 'Enumeration': ?>
												<?php foreach($column['possiblevalues'] as $possibleValue): ?>
													<div><input type="radio" id="ScheduledEventInstance_<?php echo $scheduledEventInstanceType.$columnName ?>" name="ScheduledEventInstance_<?php echo $scheduledEventInstanceType.$columnName ?>" value="<?php echo $possibleValue ?>"<?php if($scheduledEventInstance->getColumnDefault($columnName) == $possibleValue) echo ' checked="true"' ?>>
														<?php echo $possibleValue ?>
													</div>
												<?php endforeach; ?>
											<?php break;
									
											case 'Boolean': ?>
												<input type="checkbox" id="ScheduledEventInstance_<?php echo $scheduledEventInstanceType.$columnName ?>" name="ScheduledEventInstance_<?php echo $scheduledEventInstanceType.$columnName ?>"<?php if($scheduledEventInstance->getColumnDefault($columnName)) echo ' checked="checked"' ?>>
											<?php break;
									
											case 'Integer': ?>
												<input type="text" name="ScheduledEventInstance_<?php echo $scheduledEventInstanceType.$columnName ?>" id="ScheduledEventInstance_<?php echo $scheduledEventInstanceType.$columnName ?>"<?php if(($default = $scheduledEventInstance->getColumnDefault($columnName)) != null) echo ' value="'.$default.'"' ?> class="inputField">
											<?php break;
									
											case 'Date': ?>
												<input type="text" id="ScheduledEventInstance_<?php echo $scheduledEventInstanceType.$columnName ?>" name="ScheduledEventInstance_<?php echo $scheduledEventInstanceType.$columnName ?>" value="" class="inputField datePickerInput"<?php if(($default = $scheduledEventInstance->getColumnDefault($columnName)) != null) echo ' value="'.$default.'"' ?>>
											<?php break; ?>
										
										<?php } ?>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<div id="ScheduledEventInstance_tabs_Time">
				<table>
					<tr>
						<td style="text-align: right">
							<label for="ScheduledEventInstance_startDate">Start Time:</label>
						</td>
						<td>
							<input id="ScheduledEventInstance_startDate" type="text" style="width: 75px" disabled="disabled"></input>
							<input id="ScheduledEventInstance_startTime" type="text" style="width: 65px"></input>
						</td>
					</tr>
					<tr>
						<td style="text-align: right">
							<label for="ScheduledEventInstance_endDate">End Time:</label>
						</td>
						<td>
							<input id="ScheduledEventInstance_endDate" type="text" style="width: 75px" disabled="disabled"></input>
							<input id="ScheduledEventInstance_endTime" type="text" style="width: 65px"></input>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div id="ScheduledEventInstance_removeScheduledEventInstance" style="text-align: right">
			This event has been changed from the series. <button id="ScheduledEventInstance_removeScheduledEventInstance_button">Undo Changes</button>
		</div>
		<input id="ScheduledEventInstance_selectedScheduledEventInstanceId" type="hidden"></input>
		<input id="ScheduledEventInstance_selectedScheduledEventInstanceType" type="hidden"></input>
		<input id="ScheduledEventInstance_selectedScheduledEventId" type="hidden"></input>
	</div>

<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();                                 # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
