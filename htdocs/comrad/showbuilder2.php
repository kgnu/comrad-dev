<?php

	require_once('initialize.php');

	$seiid = $uri->getKey('seiid');

	if (!$seiid) {
		$seid = $uri->getKey('seid');
		$sdt = $uri->getKey('sdt');
		if ($seid && $sdt) {
			// Find all of the ScheduledEventInstances with this ScheduledEventId
			$possibleScheduledShowInstances = DB::getInstance('MySql')->find(new ScheduledShowInstance(array(
				'ScheduledEventId' => $seid
			)), $count, array('limit' => false));

			// See if one of them falls on the day specified by $sdt
			$dateString = date('dmY', $sdt);
			foreach ($possibleScheduledShowInstances as $possibleScheduledShowInstance) {
				if (date('dmY', $possibleScheduledShowInstance->StartDateTime) == $dateString) {
					$seiid = $possibleScheduledShowInstance->Id;
					break;
				}
			}

			// If we did not find a matching Scheduled Event Instance, create a new one.
			if (!$seiid) {
				$se = DB::getInstance('MySql')->find(new ScheduledEvent(array(
					'Id' => $seid
				)));

				if (count($se) > 0) {
					$se = $se[0];
					$se->fetchForeignKeyItem('TimeInfo');
					$seiid = DB::getInstance('MySql')->insert(new ScheduledShowInstance(array(
						'ScheduledEventId' => $seid,
						'StartDateTime' => (int)$sdt,
						'Duration' => $se->TimeInfo->Duration
					)));
				}
			}

			// If we now have a ScheduledEventInstance, redirect to that.  Otherwise, bail.
			if ($seiid) {
				$jump_uri = new UriBuilder('showbuilder2.php?seiid='.$seiid);
				$jump_uri->redirect();
			} else {
				exit();
			}
		} else {
			exit();
		}
	}

	// TODO: Replace with 'get' method
	$results = DB::getInstance('MySql')->find(new ScheduledShowInstance(array('Id' => $seiid)));
	$scheduledShowInstance = $results[0];

	if (!$scheduledShowInstance) {
		echo 'Could not find Scheduled Event Instance with id: '.$seiid;
		exit();
	}

	// TODO: Add a hydrate function to AbstractDBObject class that automatically (recursively?) fetches all ForeignKeyItem types
	$scheduledShowInstance->fetchForeignKeyItem('ScheduledEvent');
	$scheduledShowInstance->ScheduledEvent->fetchForeignKeyItem('Event');

?>
<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();								   # ?>
<?php $head->write();													   # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Show Builder</title>

	<!-- For Themes:  http://www.jqueryui.com/themeroller/ -->
	<!-- Documentation:	 http://docs.jquery.com/Main_Page -->
	<!-- Demos:	 http://www.jqueryui.com/demos/ -->
	<!-- Scripting Buttons:	 http://www.filamentgroup.com/lab/styling_buttons_and_toolbars_with_the_jquery_ui_css_framework/ -->

	<link type="text/css" rel="stylesheet" href="css/jquery/autocomplete/jquery.autocomplete.css" />

	<style type="text/css">
		/* html body { font-size: 10px } */

		.dialog { display: none }

		div.field { padding-top: 3px }
		div.field label { display: inline-block; width: 90px; text-align: right; float: left; margin-top: 4px }
		div.field div.input { margin-left: 90px; margin-right: 20px }
		div.field input, div.field select { text-align: left; margin-left: 6px; width: 100% }
		div.field input[type="checkbox"] { width: auto }

		#left { float: left; width: 49%; height: 70px }
		#right { float: right; width: 49%; height: 700px }

		#scratchpad { border: 1px solid #ccc }
		#schedule { border: 1px solid #ccc }
		#addevents { clear: both; padding-top: 10px; font-size: 10px }

		#Track_edit_Album, #Track_edit_Track { width: 50% }
		#Track_edit_Track { float: right }
		#Track_edit_Album .album_fields { display: none }
		#Track_edit h4 { margin: 0px 0px 10px 0px; text-align: center }

		#Track_search_results { height: 250px; overflow: auto; border: 1px solid #ccc; margin-top: 10px;}
		#Track_search_results ul { padding: 0px; margin: 0px }
		#Track_search_results ul li { list-style-type: none; height: 80px; margin: 0px; background-color: #fff; border-bottom: 1px solid #ddd }
		#Track_search_results ul li img.albumArt { float: left; width: 60px; height: 60px; margin: 10px }
		#Track_search_results ul li div.trackDetails { margin-left: 80px; padding-top: 10px }
		#Track_search_results ul li div.trackDetails span { display: block }

		.Track_not_found { background-color: #2694e8; color: #fff; padding: 10px; font-size: 1.2em }

		span.trackName { font-size: 1.2em; font-weight: bold }
		span.artistName { font-size: 0.9em; font-style: italic; color: #333 }

		#scratchpad_list, #schedule_list { padding: 10px 15px 15px 15px; margin: 0px }
		#scratchpad_list { height: 275px; overflow: auto }
		#scratchpad_list li, #schedule_list li { list-style: none; margin: 2px; padding: 2px 5px; border: 0px; cursor: pointer }

		div.sorthandle { height: 12px; width: 18px; background: #fff; opacity: 0.5; display: inline-block; margin-right: 5px; cursor: move }

		li.Alert { background-color: darkred; border-color: darkred; color: white }
		li.Announcement { background-color: green; border-color: green; color: white }
		li.EASTest { background-color: #754C24; border-color: #754C24; color: white }
		li.Feature { background-color: royalblue; border-color: royalblue; color: white }
		li.LegalId { background-color: indigo; border-color: indigo; color: white }
		li.PSA { background-color: darkorange; border-color: darkorange; color: white }
		li.TicketGiveaway { background-color: darkgoldenrod; border-color: darkgoldenrod; color: white }
		li.Underwriting { background-color: slategray; border-color: slategray; color: white }
		li.TrackPlay { background-color: #2694E8; border-color: #2694E8; color: white }
		li.VoiceBreak { background-color: #BBBBBB; border-color: #BBBBBB; color: white }
		li.DJComment { background-color: #BBBBBB; border-color: #BBBBBB; color: white }
		#psa-tab { display: none; }

		#Track_albumMissingInfo_Genre, #Track_albumMissingInfo_Label { margin: 20px 0px }
		#Track_albumMissingInfo_Genre div.field, #Track_albumMissingInfo_Label div.field { margin: 5px 0px }
		.iTunesGenre, .iTunesCopyright { font-style: italic }

		#scratchpad_list li ul.eventDetails, #schedule_list li ul.eventDetails { background: rgba(255, 255, 255, 0.5); color: #000; margin: 0px; padding: 0px; cursor: auto; }
		#scratchpad_list li ul.eventDetails li, #schedule_list li ul.eventDetails li { cursor: auto; }
	</style>

	<script type="text/javascript" src="js/jquery/ajaxqueue/jquery.ajaxqueue.js"></script>
	<script type="text/javascript" src="js/jquery/bgiframe/jquery.bgiframe.js"></script>
	<script type="text/javascript" src="js/jquery/dimensions/jquery.dimensions.js"></script>
	<script type="text/javascript" src="js/jquery/thickbox/thickbox.js"></script>
	<script type="text/javascript" src="js/jquery/autocomplete/jquery.autocomplete.js"></script>

	<script type="text/javascript" src="js/jquery/json/jquery.json.js"></script>
	<script type="text/javascript" src="js/jquery/qtip/jquery.qtip.js"></script>

	<script type="text/javascript" src="js/jquery/tinymce/tinymce/jquery.tinymce.js"></script>
	<script type="text/javascript" src="js/tinymce/kgnutinymce.js"></script>

	<script type="text/javascript" src="js/date/format/date.format.js"></script>

	<script type="text/javascript" src="js/ajax/ajaxdbinterface.js?v=2"></script>
	<script type="text/javascript" src="js/ajax/itunessearch.js?v=2"></script>
	<script type="text/javascript" src="js/ajax/searchmusiccatalog.js"></script>
	<script type="text/javascript" src="js/ajax/findtracks.js?v=2"></script>

	<script type="text/javascript">
		var isRefreshing = false;
		var ajaxErrorHappened = false;

		$(function() {
			//register global ajax handler
			$( document ).ajaxError(function(event, jqxhr, settings, thrownError) {
				if (!ajaxErrorHappened) {
					alert('An unexpected AJAX error has occurred. Please reload the page to be sure all data was saved properly.');
					ajaxErrorHappened = true;
				}
				console.log('ajax error:');
				console.log(event);
				console.log(jqxhr);
				console.log(settings);
				console.log(thrownError);
			});

			$('#Track_albumMissingInfo').dialog({
				autoOpen: false,
				modal: true,
				closeOnEscape: true,
				resizable: false,
				width: 500,
				position: 'top',
				title: 'Additional Information Needed'
			});

			// Initialize the tabs
			$(".tabs").tabs();

			// Initialize the sortability of the scratchpad and schedule
			$('#scratchpad_list, #schedule_list').sortable({
			    // axis: 'y',
				opacity: '0.6',
				revert: 100,
				handle: '.sorthandle',
				helper: 'original',
				placeholder: 'ui-state-highlight ui-corner-all',
				forcePlaceholderSize: true,
				// connectWith: '#scratchpad_list, #schedule_list',
				update: function(event, ui) {
					startLoading();
					updateFloatingEventTimes(function() { endLoading(); });
				}
			});

			// Initialize host select change listener
			$('#host_select').change(function(eventObject) {
				startLoading();
				dbCommand('save', 'ScheduledShowInstance', 'MySql', { Id: $('#showId').val(), HostId: $('#host_select').val() }, {}, function(response) {
					endLoading();
				});
			});

			// Init TinyMCE fields
			initializeKGNUTinyMCEForSelector($('.tabs .tinymce'));

			Track_show_search();

            startLoading();
			refreshScratchpadAndSchedule(function() { endLoading(); });
		});

		function startLoading() {
		    $('.disableWhenLoading').attr('disabled', 'disabled');
		    $('#scratchpad_list, #schedule_list').sortable('option', 'disabled', true);
		}

		function endLoading() {
		    $('.disableWhenLoading').removeAttr('disabled');
		    $('#scratchpad_list, #schedule_list').sortable('option', 'disabled', false);
		}

		function getReadableEventType(eventType) {
			var readableEventTypes = {
				'LegalId': 'Legal Id',
				'TicketGiveaway': 'Giveaway',
				'TrackPlay': 'Track'
			};

			return (readableEventTypes[eventType] ? readableEventTypes[eventType] : eventType);
		}
	</script>

<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();								   # ?>
<?php $body->write();													   # ?>
<?php ###################################################################### ?>
	<div>
		<table style="width: 100%; white-space: nowrap">
			<tr>
				<td style="width: 25%">
					<h4><?php echo $scheduledShowInstance->ScheduledEvent->Event->Title ?></h4>
					<a onclick="return showEditShowDescriptionDialog();" href="#" style="font-size: 0.9em">Edit today's show description</a>
				</td>
				<td style="width: 25%"><select id="host_select" class="disableWhenLoading">
					<option value="0">No Host</option>
					<?php $hosts = DB::getInstance('MySql')->find(new Host(), $count, array('limit' => false, 'sortcolumn' => 'Name')); ?>
					<?php foreach ($hosts as $host): ?>
					<option value="<?php echo $host->UID ?>"<?php if ($scheduledShowInstance->HostId == $host->UID || ($scheduledShowInstance->HostId === null && $scheduledShowInstance->ScheduledEvent->Event->HostId == $host->UID)) echo ' selected="selected"' ?>><?php echo $host->Name ?></option>
					<?php endforeach; ?>
				</select></td>
				<td style="width: 25%"><?php if (substr($scheduledShowInstance->ScheduledEvent->Event->URL, 0, 4) != 'http') echo 'kgnu.org/' . $scheduledShowInstance->ScheduledEvent->Event->URL; ?></td>
				<td style="width: 25%; text-align: right">
					<h5><?php echo date('l, F jS, Y', $scheduledShowInstance->StartDateTime) ?> from <?php echo date('g:i a', $scheduledShowInstance->StartDateTime) ?> to <?php echo date('g:i a', $scheduledShowInstance->StartDateTime + $scheduledShowInstance->Duration * 60) ?></h5>
					<!-- <a onclick="return showEditShowDescriptionDialog();" href="#" style="font-size: 1.0em">Edit today's show description</a> -->
				</td>
			<tr>
		</table>
		<div style="clear: both"></div>
	</div>
	<div>
		<div id="left">
			<div id="scratchpad">
				<h5>Scratchpad</h5>
				<ul id="scratchpad_list"></ul>
				<script type="text/javascript">
					var scratchpadContents = new Array();
					var scheduleContents = new Array();

					function updateFloatingEventTimes(callback) {

						var listsToUpdate = ['#schedule_list', '#scratchpad_list'];

						for (var l = 0; l < listsToUpdate.length; l++) {
							var selector = listsToUpdate[l];
							var list = $(selector).sortable('toArray');

							if (list.length > 1) {
								var split = list[0].split(':');
								var type = split[0];
								var moveToIndex = null;

								if (type != 'FloatingShowElement' && type != 'FloatingShowEvent') {
									split = split[1].split('-');
									var start = split[0];
									var end = split[1];

									for (var i = 1; i < list.length; i++) {
										var iSplit = list[i].split(':');
										var iType = iSplit[0];
										if (iType != 'FloatingShowElement') {
											iSplit = iSplit[1].split('-');
											var iStart = iSplit[0];
											var iEnd = iSplit[1];

											if (selector == '#schedule_list' && start < iStart ||
												selector == '#scratchpad_list' && start > iStart) {
												moveToIndex = i;
											}
										}
									}

									if (moveToIndex !== null) {
										var itemToMove = list.splice(0, 1)[0];
										list.splice(moveToIndex, 0, itemToMove);
									}
								}
							}

							if (selector == '#schedule_list') list = list.reverse();

							var split, type, end, floatingShowElements = new Array();
							var start = parseInt($('#showStartTime').val());
							var updates = {};
							for (var i = 0; i < list.length; i++) {
								split = list[i].split(':');
								type = split[0];
								if (type == 'FloatingShowElement') {
									floatingShowElements.push(split[1]);
									if (i == list.length - 1) {
										var interval = (parseInt($('#showEndTime').val()) - start) / (floatingShowElements.length + 1);
										for (var t = 0; t < floatingShowElements.length; t++) {
											updates[floatingShowElements[t]] = start + ((t + 1) * interval);
										}
									}
								} else {
									split = split[1].split('-');
									var diff = parseInt(split[0]) - start;
									if (diff > 0 && floatingShowElements.length > 0) {
	    								var interval = diff / (floatingShowElements.length + 1);
	    								for (var t = 0; t < floatingShowElements.length; t++) {
	    									updates[floatingShowElements[t]] = start + ((t + 1) * interval);
	    								}
	    								floatingShowElements = new Array();
									}
	    							start = parseInt(split[0]);
								}
							}

							// Update all of the FloatingShowElements
							var success = true, startdatetime;
							var queries = [];
							for (var floatingShowElementId in updates) {
								startdatetime = new Date(updates[floatingShowElementId] * 1000).format('yyyy-mm-dd HH:MM:ss');
								var attributes = { Id: floatingShowElementId };
								if (selector == '#schedule_list') { // Schedule
									attributes.Executed = startdatetime;
								} else { // Scratchpad
									attributes.StartDateTime = startdatetime;
									attributes.Executed = 0;
								}

								queries.push({
									method: 'update',
									params: $.toJSON({
										Type: 'FloatingShowElement',
										Attributes: attributes,
										Options: {}
									})
								});
							}

							dbCommandMultiple('update', queries, function(response) {
								if (!response || response.error) {
									success = false;
								}
							});
						}

						refreshScratchpadAndSchedule(callback);
					}

					function renderEventInstanceListElement(eventInstance) {
						var instanceId = eventInstance.Attributes.Id;
						var scheduledEventId = eventInstance.Attributes.ScheduledEvent.Attributes.Id;
						var type = eventInstance.Attributes.ScheduledEvent.Attributes.Event.Type.substr(0, eventInstance.Attributes.ScheduledEvent.Attributes.Event.Type.length - 5);
						var title = eventInstance.Attributes.ScheduledEvent.Attributes.Event.Attributes.Title;
						var startDateTime = new Date(eventInstance.Attributes.StartDateTime * 1000);
						var duration = eventInstance.Attributes.Duration;
						var endDateTime = new Date(startDateTime.getTime() + duration * 60 * 1000);
						var executedDateTime;
						if (eventInstance.Attributes.Executed > 0) executedDateTime = new Date(eventInstance.Attributes.Executed * 1000);

						// Collect the event details
						var eventDetails = {};

						// ... from the Event first
						for (var key in eventInstance.Attributes.ScheduledEvent.Attributes.Event.Attributes) {
							// TODO: Make a more maintainable attribute filtering system
							if (key == 'PSACategory') {
								eventDetails[key] = eventInstance.Attributes.ScheduledEvent.Attributes.Event.Attributes[key].Attributes.Title;
							} else if (key != 'Id' && key != 'Title' && key != 'Active' && key != 'StartDate' && key != 'KillDate' && key != 'PSACategoryId') {
								if (eventInstance.Attributes.ScheduledEvent.Attributes.Event.Attributes[key] != null) eventDetails[key] = eventInstance.Attributes.ScheduledEvent.Attributes.Event.Attributes[key];
							}
						}

						// ... then from the Scheduled Event Instance
						for (var key in eventInstance.Attributes) {
							// TODO: Make a more maintainable attribute filtering system
							if (key == 'PSACategory') {
								eventDetails[key] = eventInstance.Attributes.Event.Attributes[key].Attributes.Title;
							} else if (key != 'Duration' && key != 'StartDateTime' && key != 'ScheduledEvent' && key != 'ScheduledEventId' && key != 'Id' && key != 'Executed') {
								if (eventInstance.Attributes[key] != null) eventDetails[key] = eventInstance.Attributes[key];
							}
						}

						// Create the event details list element
						var eventDetailsList = $('<ul class="eventDetails ui-corner-all"></ul>').click(function(e) { 
              e.stopPropagation(); //don't collapse the element when clicking into the details, in case the user is clicking a link or wants to highlight something
            }).hide();
						for (var key in eventDetails) {
							if (key != 'NoCallers' && key != 'WinnerName' && key != 'WinnerPhone' && key != 'WinnerEmail' && 
								key != 'WinnerAddress' && key != 'IsListenerMember' && key != 'ShowName' && key != 'ShowDate' && key != 'Venue') {
								let displayText = key;
								if (key == 'Copy') {
									displayText = 'Show Details';
								} else if (key == 'NotesToDJ') {
									displayText = "Notes to DJ";
								}
								eventDetailsList.append(
									'<li><strong>' + displayText + ': </strong>' + eventDetails[key] + '</li>'
								);
							}
						}

						var elementId = type + ':' + startDateTime.getTime() / 1000 + '-' + endDateTime.getTime() / 1000;

						if (type == 'TicketGiveaway') {
							let attributes = eventInstance['Attributes'];
							let showAttributes = attributes['ScheduledEvent']['Attributes']['Event']['Attributes'];
							let giveawayAttributes = {
								"Copy": "",
								"TicketType": "",
								"ShowName": "",
								"Venue": "",
								"ShowDate": "",
							};
							Object.keys(giveawayAttributes).forEach(k => {
								if (attributes[k] != null && attributes[k].length > 0) {
									// take from the event instance first
									giveawayAttributes[k] = attributes[k];
								} else {
									giveawayAttributes[k] = showAttributes[k];
								}
							});
							let ticketTypeMapping = {
								'Guest List Ticket': 'Guest List',
								'Digital Ticket': 'Digital',
								'Paper Ticket': 'Paper',
								'Other Giveaway': 'Other'
							};
							if (giveawayAttributes['TicketType'] in ticketTypeMapping) {
								giveawayAttributes['TicketType'] = ticketTypeMapping[giveawayAttributes['TicketType']];
							}
							if (giveawayAttributes['Copy'] != null) {
								let newCopy = giveawayAttributes['Copy'];
								// strip html
								newCopy = newCopy.replaceAll('<br />', '\n').replaceAll('</p>', '\n').trim('');
							    var tmp = document.createElement('DIV');
								tmp.innerHTML = newCopy;
								giveawayAttributes['Copy'] = tmp.textContent || tmp.innerText || '';
							}
							if (typeof giveawayAttributes['ShowDate'] == 'number') {
								giveawayAttributes['ShowDate'] = new Date(giveawayAttributes['ShowDate'] * 1000).toLocaleDateString();
							}
							giveawayLi = $('<li><button ' +
												(!instanceId ? ' disabled="disabled"' : 'onclick="window.open(\'<?php echo $init->getProp('JotformUrl'); ?>?' +
													'venue=' + encodeURIComponent(giveawayAttributes['Venue']) + '&showArtist=' + encodeURIComponent(giveawayAttributes['ShowName']) +
													'&showInfo=' + encodeURIComponent(giveawayAttributes['Copy']).replace(/\"/g,"\\\"").replace(/\'/g, "\\'") + '&showTime=' +
													'&showDate=' + encodeURIComponent(giveawayAttributes['ShowDate']) + 
													'&ticketType=' + encodeURIComponent(giveawayAttributes['TicketType']) + '\', \'giveaway\', \'width=550, height=650\');"') +
												' class="WinnerInfo">Enter Winner Information</button></li>');
							eventDetailsList.append(giveawayLi);

							if (!instanceId) {
								var scheduledEventInstance = {
									'Type': 'Scheduled' + type + 'Instance',
									'Attributes': {
										'StartDateTime': startDateTime.format('yyyy-mm-dd HH:MM:ss'),
										'Duration': duration,
										'ScheduledEventId': scheduledEventId
									}
								};

								dbCommand('save', scheduledEventInstance.Type, 'MySql', scheduledEventInstance.Attributes, {}, function(response) {
									if (response && !response.error) {
										scheduledEventInstance.Attributes.Id = response.Id;
										$("[id='" + elementId + "'] button.WinnerInfo").attr("onclick", "window.open('giveaway.php?seiid=" + response.Id + "\', \'giveaway\', \'width=550, height=650\');").
											attr("disabled", false);
										$("[id='" + elementId + "'] button.AddToSavedItems").data("scheduledEventInstance", scheduledEventInstance);
									}
								});
							}
						}

						// Create the event element
						var element = $('<li class="' + type + ' ui-corner-all"></li>').append(
						    (!executedDateTime || (executedDateTime && type == 'LegalId') || true ? startDateTime.format('h:MM tt') + ' - ' : '')
						).append(
							'<strong>' + getReadableEventType(type) + ': </strong>'
						).append(
							'<em>' + title + '</em>'
						).append(
							'<div style="clear:both"></div>'
						).append(
							eventDetailsList
						).click(function() {
							eventDetailsList.toggle();
						}).attr(
							'id', elementId
						);

						if (executedDateTime) {
							element.prepend(
								$('<button class="disableWhenLoading" style="float: right">&lt;-</button>').click(function() {
									if (confirm('Are you sure?')) {
										var listElement = $(this).parent();
									    startLoading();
										setScheduledEventInstanceExecutedAndSave(eventInstance, false, function(response) {
											if (response && !response.error) {
												$('#scratchpad_list').prepend(listElement);
												updateFloatingEventTimes(function() { endLoading(); });
											}
										});
									}
									return false;
								})
							);
						} else {
							element.prepend(
								$('<button class="disableWhenLoading AddToSavedItems" style="float: right">-&gt;</button>').click(function() {
									// if (new Date() < new Date($('#showStartTime').val() * 1000)) {
										// alert('This operation is not available before the show starts.');
										// return false;
									// }

									var listElement = $(this).parent();

									// If it's not an instance, create a new instance
									if (!instanceId && !$(this).data("scheduledEventInstance")) {
										var scheduledEventInstance = {
											'Type': 'Scheduled' + type + 'Instance',
											'Attributes': {
												'StartDateTime': startDateTime.format('yyyy-mm-dd HH:MM:ss'),
												'Duration': duration,
												'ScheduledEventId': scheduledEventId
											}
										};

									    startLoading();
										dbCommand('save', scheduledEventInstance.Type, 'MySql', scheduledEventInstance.Attributes, {}, function(response) {
											if (response && !response.error) {
												scheduledEventInstance.Attributes.Id = response.Id;
												setScheduledEventInstanceExecutedAndSave(scheduledEventInstance, true, function(response) {
													if (response && !response.error) {
														$('#schedule_list').prepend(listElement);
														updateFloatingEventTimes(function() { endLoading(); });
													}
												});
											}
										});
									} else {
									    startLoading();
										if ($(this).data("scheduledEventInstance")) {
											eventInstance = $(this).data("scheduledEventInstance");
										}
										setScheduledEventInstanceExecutedAndSave(eventInstance, true, function(savedScheduledEventInstance) {
											if (savedScheduledEventInstance && !savedScheduledEventInstance.error) {
												$('#schedule_list').prepend(listElement);
												updateFloatingEventTimes(function() { endLoading(); });
											}
										});
									}
									return false;
								})
							);
						}

						// Don't allow sorting of prescheduled events for now
						// element.prepend(
						// 	$('<div class="sorthandle ui-corner-all"></div>').click(function() { return false })
						// );

						return element;
					}

					function renderFloatingShowEventListElement(floatingShowEvent) {
						var floatingShowEventId = floatingShowEvent.Attributes.Id;
						var type = floatingShowEvent.Attributes.Event.Type.substr(0, floatingShowEvent.Attributes.Event.Type.length - 5);
						var title = floatingShowEvent.Attributes.Event.Attributes.Title;
						var startDateTime = new Date(floatingShowEvent.Attributes.StartDateTime * 1000);
						var executedDateTime;
						if (floatingShowEvent.Attributes.Executed > 0) executedDateTime = new Date(floatingShowEvent.Attributes.Executed * 1000);

						// Collect the event details
						var eventDetails = {};

						// ... from the Event first
						for (var key in floatingShowEvent.Attributes.Event.Attributes) {
							// TODO: Make a more maintainable template or attribute filtering system
							if (key == 'PSACategory') {
								eventDetails[key] = floatingShowEvent.Attributes.Event.Attributes[key].Attributes.Title;
							} else if (key != 'Id' && key != 'Title' && key != 'Active' && key != 'StartDate' && key != 'KillDate' && key != 'PSACategoryId') {
								if (floatingShowEvent.Attributes.Event.Attributes[key] != null) eventDetails[key] = floatingShowEvent.Attributes.Event.Attributes[key];
							}
						}

						// Create the event details list element
						var eventDetailsList = $('<ul class="eventDetails ui-corner-all"></ul>').click(function(e) { 
              e.stopPropagation(); //don't collapse the element when clicking into the details, in case the user is clicking a link or wants to highlight something
            }).hide();
						for (var key in eventDetails) {
							eventDetailsList.append(
								'<li><strong>' + key + ': </strong>' + eventDetails[key] + '</li>'
							);
						}

						var element = $('<li class="' + type + ' ui-corner-all"></li>').append(
							'<strong>' + getReadableEventType(type) + ': </strong>'
						).append(
							'<em>' + title + '</em>'
						).append(
							(!executedDateTime && false ? ' (' + startDateTime.format('h:MM tt') + ')' : '')
						).append(
							'<div style="clear:both"></div>'
						).append(
							eventDetailsList
						).click(function() {
							eventDetailsList.toggle();
						}).attr(
							'id', 'FloatingShowElement:' + floatingShowEventId
						);

						if (executedDateTime) {
							element.prepend(
								$('<button class="disableWhenLoading" style="float: right">&lt;-</button>').click(function(event) {
									if (confirm('Are you sure?')) {
										var listElement = $(this).parent();
									    startLoading();
										setFloatingShowElementExecutedAndSave(floatingShowEvent, false, function(response) {
											if (response && !response.error) {
												$('#scratchpad_list').prepend(listElement);
												updateFloatingEventTimes(function() { endLoading(); });
											}
										});
									}
									return false;
								})
							);
						} else {
							element.prepend(
								$('<button class="disableWhenLoading" style="float: right">X</button>').click(function() {
									if (confirm('Are you sure?')) {
									    startLoading();
										dbCommand('delete', floatingShowEvent.Type, 'MySql', floatingShowEvent.Attributes, {}, function(response) {
											if (response && response.error) {
												alert(response.error);
											}

											refreshScratchpadAndSchedule(function() {
											    updateFloatingEventTimes(function() {endLoading(); })
											});
										});
									}
									return false;
								})
							).prepend(
								$('<button class="disableWhenLoading" style="float: right">-&gt;</button>').click(function() {
									// if (new Date() < new Date($('#showStartTime').val() * 1000)) {
										// alert('This operation is not available before the show starts.');
										// return false;
									// }
									var listElement = $(this).parent();
									startLoading();
									setFloatingShowElementExecutedAndSave(floatingShowEvent, true, function(response) {
										if (response && !response.error) {
											$('#schedule_list').prepend(listElement);
											updateFloatingEventTimes(function() { endLoading(); });
										}
									});
									return false;
								})
							);
						}

						element.prepend(
							$('<div class="sorthandle ui-corner-all"></div>').click(function() { return false })
						);

						return element;
					}

					function renderTrackPlayListElement(trackPlay) {
						var trackPlayId = trackPlay.Attributes.Id;
						var title = trackPlay.Attributes.Track.Attributes.Title;
						var album = trackPlay.Attributes.Track.Attributes.Album.Attributes.Title;
						var albumid = trackPlay.Attributes.Track.Attributes.AlbumID;
						var artist = (trackPlay.Attributes.Track.Attributes.Artist ? trackPlay.Attributes.Track.Attributes.Artist : trackPlay.Attributes.Track.Attributes.Album.Attributes.Artist);
						var startDateTime = new Date(trackPlay.Attributes.StartDateTime * 1000);
						var executedDateTime;
						if (trackPlay.Attributes.Executed > 0) executedDateTime = new Date(trackPlay.Attributes.Executed * 1000);

						// Collect the event details
						var eventDetails = {
							'Track Title': title,
							'Album': album,
							'Artist': artist
							// 'Genre': trackPlay.Attributes.Track.Attributes.Album.Attributes.Genre.Attributes.Name
						};

						// Create the event details list element
						var eventDetailsList = $('<ul class="eventDetails ui-corner-all"></ul>').click(function(e) { 
              e.stopPropagation(); //don't collapse the element when clicking into the details, in case the user is clicking a link or wants to highlight something
            }).hide();
						for (var key in eventDetails) {
							eventDetailsList.append(
								'<li><strong>' + key + ': </strong>' + eventDetails[key] + '</li>'
							);
						}

						var element = $('<li class="TrackPlay ui-corner-all"></li>').append(
							'<strong>Track: </strong>'
						).append(
							'<em>' + title + '</em> by '
						).append(
							'<em>' + artist + '</em>'
						).append(
							(!executedDateTime && false ? ' (' + startDateTime.format('h:MM tt') + ')' : '')
						).append(
							'<div style="clear:both"></div>'
						).append(
							eventDetailsList
						).click(function() {
							eventDetailsList.toggle();
						}).attr(
							'id', 'FloatingShowElement:' + trackPlayId
						);

						if (executedDateTime) {
							element.prepend(
								$('<button class="disableWhenLoading" style="float: right">&lt;-</button>').click(function(event) {
									if (confirm('Are you sure?')) {
										var listElement = $(this).parent();
									    startLoading();
										setFloatingShowElementExecutedAndSave(trackPlay, false, function(response) {
											if (response && !response.error) {
												$('#scratchpad_list').prepend(listElement);
												updateFloatingEventTimes(function() { endLoading(); });
											}
										});
									}
									return false;
								})
							);
						} else {
							element.prepend(
								$('<button class="disableWhenLoading" style="float: right">X</button>').click(function() {
									if (confirm('Are you sure?')) {
									    startLoading();
										dbCommand('delete', trackPlay.Type, 'MySql', trackPlay.Attributes, {}, function(response) {
											if (response && response.error) {
												alert(response.error);
											}

											refreshScratchpadAndSchedule(function() {
											    updateFloatingEventTimes(function() { endLoading(); });
											});
										});
									}
									return false;
								})
							).prepend(
								$('<button class="disableWhenLoading" style="float: right">-&gt;</button>').click(function() {
									// if (new Date() < new Date($('#showStartTime').val() * 1000)) {
										// alert('This operation is not available before the show starts.');
										// return false;
									// }
									var listElement = $(this).parent();
									startLoading();
									setFloatingShowElementExecutedAndSave(trackPlay, true, function(response) {
										if (response && !response.error) {
											$('#schedule_list').prepend(listElement);
											updateFloatingEventTimes(function() { endLoading(); });
										}
									});
									return false;
								})
							);
						}

						element.prepend(
							$('<div class="sorthandle ui-corner-all"></div>').click(function() { return false })
						);

						return element;
					}

					function renderVoiceBreakListElement(voiceBreak) {
						var voiceBreakId = voiceBreak.Attributes.Id;
						var startDateTime = new Date(voiceBreak.Attributes.StartDateTime * 1000);
						var executedDateTime;
						if (voiceBreak.Attributes.Executed > 0) executedDateTime = new Date(voiceBreak.Attributes.Executed * 1000);

						var element = $('<li class="VoiceBreak ui-corner-all"></li>').append(
							'<strong>Voice Break</strong>'
						).append(
							(!executedDateTime && false ? ' (' + startDateTime.format('h:MM tt') + ')' : '')
						).attr(
							'id', 'FloatingShowElement:' + voiceBreakId
						).append(
							'<div style="clear:both"></div>'
						);

						if (executedDateTime) {
							element.prepend(
								$('<button class="disableWhenLoading" style="float: right">&lt;-</button>').click(function(event) {
									if (confirm('Are you sure?')) {
										var listElement = $(this).parent();
									    startLoading();
										setFloatingShowElementExecutedAndSave(voiceBreak, false, function(response) {
											if (response && !response.error) {
												$('#scratchpad_list').prepend(listElement);
												updateFloatingEventTimes(function() { endLoading(); });
											}
										});
									}
									return false;
								})
							);
						} else {
							element.prepend(
								$('<button class="disableWhenLoading" style="float: right">X</button>').click(function() {
									if (confirm('Are you sure?')) {
									    startLoading();
										dbCommand('delete', voiceBreak.Type, 'MySql', voiceBreak.Attributes, {}, function(response) {
											if (response && response.error) {
												alert(response.error);
											}

											refreshScratchpadAndSchedule(function() {
											    updateFloatingEventTimes(function() { endLoading(); });
											});
										});
									}
									return false;
								})
							).prepend(
								$('<button class="disableWhenLoading" style="float: right">-&gt;</button>').click(function() {
									// if (new Date() < new Date($('#showStartTime').val() * 1000)) {
										// alert('This operation is not available before the show starts.');
										// return false;
									// }
									var listElement = $(this).parent();
									startLoading();
									setFloatingShowElementExecutedAndSave(voiceBreak, true, function(response) {
										if (response && !response.error) {
											$('#schedule_list').prepend(listElement);
											updateFloatingEventTimes(function() { endLoading(); });
										}
									});
									return false;
								})
							);
						}

						element.prepend(
							$('<div class="sorthandle ui-corner-all"></div>').click(function() { return false })
						);

						return element;
					}

					function renderDJCommentListElement(djComment) {
						var djCommentId = djComment.Attributes.Id;
						var body = djComment.Attributes.Body;
						var startDateTime = new Date(djComment.Attributes.StartDateTime * 1000);
						var executedDateTime;
						if (djComment.Attributes.Executed > 0) executedDateTime = new Date(djComment.Attributes.Executed * 1000);

						// Create the event details list element
						var eventDetailsList = $('<ul class="eventDetails ui-corner-all"></ul>').click(function(e) { 
              e.stopPropagation(); //don't collapse the element when clicking into the details, in case the user is clicking a link or wants to highlight something
            }).hide();
						eventDetailsList.append('<li>' + body + '</li>');

						var element = $('<li class="DJComment ui-corner-all"></li>').append(
							'<strong>Comment: </strong>'
						).append(
							(!executedDateTime && false ? ' (' + startDateTime.format('h:MM tt') + ')' : '')
						).append(
							'<div style="clear:both"></div>'
						).append(
							eventDetailsList
						).click(function() {
							eventDetailsList.toggle();
						}).attr(
							'id', 'FloatingShowElement:' + djCommentId
						);

						if (executedDateTime) {
							element.prepend(
								$('<button class="disableWhenLoading" style="float: right">&lt;-</button>').click(function(event) {
									if (confirm('Are you sure?')) {
										var listElement = $(this).parent();
									    startLoading();
										setFloatingShowElementExecutedAndSave(djComment, false, function(response) {
											if (response && !response.error) {
												$('#scratchpad_list').prepend(listElement);
												updateFloatingEventTimes(function() { endLoading(); });
											}
										});
									}
									return false;
								})
							);
						} else {
							element.prepend(
								$('<button class="disableWhenLoading" style="float: right">X</button>').click(function() {
									if (confirm('Are you sure?')) {
									    startLoading();
										dbCommand('delete', djComment.Type, 'MySql', djComment.Attributes, {}, function(response) {
											if (response && response.error) {
												alert(response.error);
											}

											refreshScratchpadAndSchedule(function() {
											    updateFloatingEventTimes(function() { endLoading(); });
											});
										});
									}
									return false;
								})
							).prepend(
								$('<button class="disableWhenLoading" style="float: right">-&gt;</button>').click(function() {
									// if (new Date() < new Date($('#showStartTime').val() * 1000)) {
										// alert('This operation is not available before the show starts.');
										// return false;
									// }
									var listElement = $(this).parent();
									startLoading();
									setFloatingShowElementExecutedAndSave(djComment, true, function(response) {
										if (response && !response.error) {
											$('#schedule_list').prepend(listElement);
											updateFloatingEventTimes(function() { endLoading(); });
										}
									});
									return false;
								})
							);
						}

						element.prepend(
							$('<div class="sorthandle ui-corner-all"></div>').click(function() { return false })
						);

						return element;
					}

					function renderScratchpadAndSchedule() {
						$('#scratchpad_list').empty();
						$('#schedule_list').empty();

						$.each(scratchpadContents, function(index, scratchpadItem) {
							var element;
							switch (scratchpadItem.Type) {
								case 'FloatingShowEvent':
									element = renderFloatingShowEventListElement(scratchpadItem);
									break;
								case 'TrackPlay':
									element = renderTrackPlayListElement(scratchpadItem);
									break;
								case 'VoiceBreak':
									element = renderVoiceBreakListElement(scratchpadItem);
									break;
								case 'DJComment':
									element = renderDJCommentListElement(scratchpadItem);
									break;
								default:
									element = renderEventInstanceListElement(scratchpadItem);
									break;
							}

							$('#scratchpad_list').append(element);
						});

						$.each(scheduleContents, function(index, scheduleItem) {
							var element;
							switch (scheduleItem.Type) {
								case 'FloatingShowEvent':
									element = renderFloatingShowEventListElement(scheduleItem);
									break;
								case 'TrackPlay':
									element = renderTrackPlayListElement(scheduleItem);
									break;
								case 'VoiceBreak':
									element = renderVoiceBreakListElement(scheduleItem);
									break;
								case 'DJComment':
									element = renderDJCommentListElement(scheduleItem);
									break;
								default:
									element = renderEventInstanceListElement(scheduleItem);
									break;
							}

							$('#schedule_list').append(element);
						});
					}

					function refreshScratchpadAndSchedule(callback) {
						if (isRefreshing) {
							if (callback) callback();
							return;
						}
						isRefreshing = true;

						scratchpadContents = new Array();
						scheduleContents = new Array();

						$.get('ajax/geteventsbetween.php', {
							start: $('#showStartTime').val(),
							end: $('#showEndTime').val(),
							types: $.toJSON([ 'Alert', 'Announcement', 'EASTest', 'TicketGiveaway', 'Feature', 'LegalId', 'PSA', 'Underwriting' ])
						}, function(eventInstances) {
							$.get('ajax/getfloatingshowelements.php', {
								showid: $('#showId').val()
							}, function(floatingShowElements) {
								// Insert results into the schedule if they have been executed, and the scratchpad if they have not
								$.each(eventInstances.concat(floatingShowElements), function(index, value) {
									if (value.Attributes.Executed > 0) {
										scheduleContents.push(value);
									} else {
										scratchpadContents.push(value)
									}
								});

								// Sort the scratchpad contents
								scratchpadContents.sort(function(a, b) {
									return a.Attributes.StartDateTime - b.Attributes.StartDateTime;
								});

								// Sort the schedule contents
								scheduleContents.sort(function(a, b) {
									return b.Attributes.Executed - a.Attributes.Executed;
								});

								// Finally, render the scratchpad and the schedule
								renderScratchpadAndSchedule();

								isRefreshing = false;

								if (callback) callback();
							}, 'json');
						}, 'json');
					}

					function setFloatingShowElementExecutedAndSave(fse, executed, callback) {
					    var attributes = { Id: fse.Attributes.Id };
						if (executed) {
							attributes.Executed = parseInt($('#showEndTime').val());
						} else {
							attributes.Executed = 0;
						}

						dbCommand('save', fse.Type, 'MySql', attributes, {}, callback);
					}

					function setScheduledEventInstanceExecutedAndSave(sei, executed, callback) {
					    var attributes = { Id: sei.Attributes.Id };
						if (executed) {
							attributes.Executed = sei.Attributes.StartDateTime;
						} else {
							attributes.Executed = 0;
						}

						dbCommand('save', sei.Type, 'MySql', attributes, {}, callback);
					}
				</script>
			</div>
			<div id="addevents">
				<div class="tabs">
					<ul>
						<li><a href="#Track_tab">Track</a></li>
						<li><a id="psa-tab" href="#PSA_tab">PSA</a></li>
						<!-- <li><a href="#TicketGiveaway_tab">Ticket Giveaway</a></li> -->
						<li><a href="#DJComment_tab">Comment</a></li>
						<li><a href="#VoiceBreak_tab">Voice Break</a></li>
					</ul>

					<div id="Track_tab">
						<script type="text/javascript">
							$(function() {
								$('#Track_edit_AlbumCompilation').change(function(event) {
									if ($('#Track_edit_AlbumCompilation').is(':checked')) {
										if ($('#Track_edit_AlbumArtist').parent().parent().is(':visible')) {
											$('#Track_edit_AlbumArtist').parent().parent().hide(100);
											$('#Track_edit_TrackArtist').val($('#Track_edit_AlbumArtist').val()).parent().parent().show(100);
										}
									} else {
										if ($('#Track_edit_TrackArtist').parent().parent().is(':visible')) {
											$('#Track_edit_TrackArtist').parent().parent().hide(100);
											$('#Track_edit_AlbumArtist').val($('#Track_edit_TrackArtist').val()).parent().parent().show(100);
										}
									}
								});

								$('#Track_edit_AlbumSearch').autocomplete('ajax/autocomplete/albums.php', {
									minChars: 1,
									cacheLength: 0,
									extraParams: {
										allowcreatenew: true
									},
									max: 0,
									mustMatch: false,
									matchSubset: false,
									delay: 10
								}).result(function(event, item) {
								    if (item && item.length == 2) {
										var album = $.evalJSON(item[1]);

										$('#Track_edit_AlbumSearch').val('').parent().parent().hide();

										$('#Track_edit_Track input').removeAttr('disabled');

										if (album.Attributes.AlbumID) {
											$('#Track_edit_Album input, #Track_edit_Album select').attr('disabled', 'disabled');
											$('#Track_edit_AlbumAlbumId').val(album.Attributes.AlbumID);
											$('#Track_edit_TrackTitle').focus();
										} else {
											$('#Track_edit_Album input, #Track_edit_Album select').removeAttr('disabled');
											$('#Track_edit_AlbumAlbumId').val('');
											$('#Track_edit_AlbumCompilation').removeAttr('checked').change();
											$('#Track_edit_AlbumArtist').focus();
										}

										$('#Track_edit_AlbumTitle').val(album.Attributes.Title ? album.Attributes.Title : '');
										$('#Track_edit_AlbumArtist').val(album.Attributes.Artist ? album.Attributes.Artist : '');
										$('#Track_edit_AlbumLabel').val(album.Attributes.Label ? album.Attributes.Label : '');
										// $('#Track_edit_AlbumGenreID').val(album.Attributes.GenreID ? album.Attributes.GenreID : '');
										if (album.Attributes.Compilation) {
											$('#Track_edit_AlbumCompilation').attr('checked', 'checked').change();
											$('#Track_edit_AlbumArtist').parent().parent().hide();
											$('#Track_edit_TrackArtist').parent().parent().show();
										} else {
											$('#Track_edit_AlbumCompilation').removeAttr('checked').change();
											$('#Track_edit_TrackArtist').parent().parent().hide();
											$('#Track_edit_AlbumArtist').parent().parent().show();
										}

										$('#Track_edit_Album .album_fields').show();
									}
								});

								$('#Track_edit_AlbumArtist, #Track_edit_TrackArtist').autocomplete('ajax/autocomplete/artists.php', {
									minChars: 2,
									cacheLength: 0,
									max: 0,
									mustMatch: false,
									matchSubset: false,
									delay: 10
								}).result(function(event, item) {
									if (item && item.length == 1) {
										$('#Track_edit_AlbumArtist').val(item[0]);
									}
								});

								$('#Track_edit_AlbumLabel').autocomplete('ajax/autocomplete/labels.php', {
									minChars: 2,
									cacheLength: 0,
									max: 0,
									mustMatch: false,
									matchSubset: false,
									delay: 10
								}).result(function(event, item) {
									if (item && item.length == 1) {
										$('#Track_edit_AlbumLabel').val(item[0]);
									}
								});
							});

							function Track_show_search() {
								$('#Track_search').show();
								$('#Track_edit').hide();
								$('#Track_preview').hide();
							}

							function Track_submit_search() {
								$('#Track_search_keywords').blur();
								$('#Track_search_keywords').attr('disabled', true);
								$('#Track_search_results').html('<div style="text-align: center; margin-top: 90px"><h4>Searching</h4><img src="media/ajax.gif" title="Searching..." alt="Searching..."></div>');

								var cdCodeResults = null;
								var localResults = null;
								var iTunesResults = null;

								if ($('#Track_search_keywords').val().match(/^[\d]+$/)) {
									var albumid = parseInt($('#Track_search_keywords').val());
									findTracksInAlbum({
										'albumid': albumid,
										'limit': 0
									}, function(results) {
										Track_load_search_results(results);
									});
									return;
								}

								startLoading();
								findTracksFromCatalogOrITunes($('#Track_search_keywords').val(), 0, function(results) {
									endLoading();
									Track_load_search_results(results);
								});
							}

							function Track_fetch_more_search_results() {
								if ($('.load_more_results img').is(':visible')) return;

								$('.load_more_results img').show();
								findTracksFromCatalogOrITunes($('#Track_search_keywords').val(), $('#Track_search_results').children('ul').length, function(results) {
									if (results === false || results.length < 30) {
										$('.load_more_results').hide();
									} else {
										$('.load_more_results').show();

										var image;

										$.each(results, function(i, track) {

											// Find the correct image to show for the Track
											if (track.Attributes.Album.Attributes.AlbumArt) {
												image = track.Attributes.Album.Attributes.AlbumArt;
											} else {
												if (track.Type == 'TrackFromITunes') {
													image = 'media/itunes.png';
												} else if (track.Type == 'Track') {
													image = 'media/kgnu.png';
												}
											}

											// Show all of the tracks
											$('#Track_search_results ul:last').after(
												$('<ul></ul>').append(
													$('<li class="Track_search_result"></li>').append(
														$('<img class="albumArt" src="' + image + '" />')
													).append(
														$('<div style="float: right; margin-top: 10px; margin-right: 10px"></div>').append(
															$('<button class="disableWhenLoading" style="font-weight: bold; width: 150px; display: block">Add to Scratchpad</button>').click(function(event) {
																startLoading();
																if (track.Type == 'Track') {
																	Track_submit_trackAndTrackPlay(track);
																	// Track_submit_trackPlay(track.Attributes.TrackID);
																} else {
																	getITunesAlbumInfo(track.Attributes.Album.Attributes.ITunesCollectionId, function(albumInfo) {
																		if (albumInfo.copyright) track.Attributes.Album.Attributes.Copyright = albumInfo.copyright;
																		if (albumInfo.collectionType) track.Attributes.Album.Attributes.Compilation = (albumInfo.collectionType == 'Compilation');
																		if (track.Attributes.Album.Attributes.Compilation) {
																			track.Attributes.Artist = track.Attributes.Album.Attributes.Artist;
																			delete(track.Attributes.Album.Attributes.Artist);
																		}
																		delete(track.Attributes.Album.Attributes.ITunesCollectionId);
																		Track_submit_trackAndTrackPlay(track);
																	});
																}
															})
														).append(
															$('<button class="disableWhenLoading" style="width: 150px; margin-top: 3px; display: block">Add to Saved Items</button>').click(function(event) {
																// if (new Date() < new Date($('#showStartTime').val() * 1000)) {
																	// alert('This operation is not available before the show starts.');
																	// return false;
																// }
																startLoading();
																if (track.Type == 'Track') {
																	Track_submit_trackAndTrackPlay(track, true);
																	// Track_submit_trackPlay(track.Attributes.TrackID, true);
																} else {
																	getITunesAlbumInfo(track.Attributes.Album.Attributes.ITunesCollectionId, function(albumInfo) {
																		if (albumInfo.copyright) track.Attributes.Album.Attributes.Copyright = albumInfo.copyright;
																		if (albumInfo.collectionType) track.Attributes.Album.Attributes.Compilation = (albumInfo.collectionType == 'Compilation');
																		if (track.Attributes.Album.Attributes.Compilation) {
																			track.Attributes.Artist = track.Attributes.Album.Attributes.Artist;
																			delete(track.Attributes.Album.Attributes.Artist);
																		}
																		delete(track.Attributes.Album.Attributes.ITunesCollectionId);
																		Track_submit_trackAndTrackPlay(track, true);
																	});
																}
															})
														)
													).append(
														$('<div class="trackDetails"></div>').append(
															$('<span class="trackName">' + track.Attributes.Title + '</span>')
														).append(
															$('<span class="albumName">' + track.Attributes.Album.Attributes.Title + (track.Attributes.Album.Attributes.AlbumID ? ' (KGNU CD #' + track.Attributes.Album.Attributes.AlbumID + ')' : '') + '</span>')
														).append(
															$('<span class="artistName">' + (track.Attributes.Artist ? track.Attributes.Artist : (track.Attributes.Album.Attributes.Artist ? track.Attributes.Album.Attributes.Artist : 'Unknown Artist')) + '</div>')
														)
													).mouseover(function () {
														$(this).css('background-color', '#eee');
													}).mouseout(function () {
														$(this).css('background-color', '#fff');
													})
												)
											);
										});

										$('.load_more_results img').hide();
									}
								});
							}

							function Track_load_search_results(results) {
								$('#Track_search_results').html('');

								var image;

								$.each(results, function(i, track) {

									// Find the correct image to show for the Track
									if (track.Attributes.Album.Attributes.AlbumArt) {
										image = track.Attributes.Album.Attributes.AlbumArt;
									} else {
										if (track.Type == 'TrackFromITunes') {
											image = 'media/itunes.png';
										} else if (track.Type == 'Track') {
											image = 'media/kgnu.png';
										}
									}

									// Show all of the tracks
									$('#Track_search_results').append(
										$('<ul></ul>').append(
											$('<li class="Track_search_result"></li>').append(
												$('<img class="albumArt" src="' + image + '" />')
											).append(
												$('<div style="float: right; margin-top: 10px; margin-right: 10px"></div>').append(
													$('<button class="disableWhenLoading" style="font-weight: bold; width: 150px; display: block">Add to Scratchpad</button>').click(function(event) {
														startLoading();
														if (track.Type == 'Track') {
															Track_submit_trackAndTrackPlay(track)
															// Track_submit_trackPlay(track.Attributes.TrackID);
														} else {
															getITunesAlbumInfo(track.Attributes.Album.Attributes.ITunesCollectionId, function(albumInfo) {
																if (albumInfo.copyright) track.Attributes.Album.Attributes.Copyright = albumInfo.copyright;
																if (albumInfo.collectionType) track.Attributes.Album.Attributes.Compilation = (albumInfo.collectionType == 'Compilation');
																if (track.Attributes.Album.Attributes.Compilation) {
																	track.Attributes.Artist = track.Attributes.Album.Attributes.Artist;
																	delete(track.Attributes.Album.Attributes.Artist);
																}
																delete(track.Attributes.Album.Attributes.ITunesCollectionId);
																Track_submit_trackAndTrackPlay(track);
															});
														}
													})
												).append(
													$('<button class="disableWhenLoading" style="width: 150px; margin-top: 3px; display: block">Add to Saved Items</button>').click(function(event) {
														// if (new Date() < new Date($('#showStartTime').val() * 1000)) {
															// alert('This operation is not available before the show starts.');
															// return false;
														// }
														startLoading();
														if (track.Type == 'Track') {
															Track_submit_trackAndTrackPlay(track, true);
															// Track_submit_trackPlay(track.Attributes.TrackID, true);
														} else {
															getITunesAlbumInfo(track.Attributes.Album.Attributes.ITunesCollectionId, function(albumInfo) {
																if (albumInfo.copyright) track.Attributes.Album.Attributes.Copyright = albumInfo.copyright;
																if (albumInfo.collectionType) track.Attributes.Album.Attributes.Compilation = (albumInfo.collectionType == 'Compilation');
																if (track.Attributes.Album.Attributes.Compilation) {
																	track.Attributes.Artist = track.Attributes.Album.Attributes.Artist;
																	delete(track.Attributes.Album.Attributes.Artist);
																}
																delete(track.Attributes.Album.Attributes.ITunesCollectionId);
																Track_submit_trackAndTrackPlay(track, true);
															});
														}
													})
												)
											).append(
												$('<div class="trackDetails"></div>').append(
													$('<span class="trackName">' + track.Attributes.Title + '</span>')
												).append(
													$('<span class="albumName">' + track.Attributes.Album.Attributes.Title + (track.Attributes.Album.Attributes.AlbumID ? ' (KGNU CD #' + track.Attributes.Album.Attributes.AlbumID + ')' : '') + '</span>')
												).append(
													$('<span class="artistName">' + (track.Attributes.Artist ? track.Attributes.Artist : (track.Attributes.Album.Attributes.Artist ? track.Attributes.Album.Attributes.Artist : 'Unknown Artist')) + '</div>')
												)
											).mouseover(function () {
												$(this).css('background-color', '#eee');
											}).mouseout(function () {
												$(this).css('background-color', '#fff');
											})
										)
									);
								});

								// Show the option for manual entry
								$('#Track_search_results').append(
									$('<div class="Track_not_found"></div>').append(
										$("<div><strong>Can't find what you're looking for?</strong></div>")
									).append(
										$('<span class="load_more_results"></span>').append(
											$('<img src="media/ajax2.gif" style="display: none; margin: 0px 10px" />')
										).append(
											$('<span style="text-decoration: underline; cursor: pointer">Load more search results</span>').click(function(event) {
												Track_fetch_more_search_results();
											})
										).append(' or ')
									).append(
										$('<span style="text-decoration: underline; cursor: pointer">Add it manually</span>').click(function(event) {
											Track_show_edit();
										})
									)
								);

								if (results.length < 30) {
									$('.load_more_results').hide();
								} else {
									$('.load_more_results').show();
								}

								// Re-enable the search input
								$('#Track_search_keywords').removeAttr('disabled');
							}

							function Track_show_edit(track) {
								// Reset the form
								$(':input', ' #Track_edit').not(':button, :submit').val('').removeAttr('checked').removeAttr('selected').removeAttr('disabled');
								$('#Track_edit_AlbumSearch').val('').parent().parent().show();
								$('#Track_edit_Album .album_fields').hide();
								$('#Track_edit_Track input').attr('disabled', 'disabled');

								if (track) {
									if (track.Attributes.Album.Attributes.Title != null) $('#Track_edit_AlbumTitle').val(track.Attributes.Album.Attributes.Title).attr('disabled', 'disabled');
									if (track.Attributes.Album.Attributes.Artist != null) $('#Track_edit_AlbumArtist').val(track.Attributes.Album.Attributes.Artist).attr('disabled', 'disabled');
									if (track.Attributes.Album.Attributes.Label != null) $('#Track_edit_AlbumLabel').val(track.Attributes.Album.Attributes.Label).attr('disabled', 'disabled');
									// if (track.Attributes.Album.Attributes.GenreID != null) $('#Track_edit_AlbumGenreID').val(track.Attributes.Album.Attributes.GenreID);
									// if (track.Attributes.Album.Attributes.Genre != null && track.Attributes.Album.Attributes.Genre.Attributes.Name != null) $("#Track_edit_AlbumGenreID").val($("#Track_edit_AlbumGenreID option:contains('" + track.Attributes.Album.Attributes.Genre.Attributes.Name + "')").val());
									if (track.Attributes.Album.Attributes.Label != null) $('#Track_edit_AlbumLabel').val(track.Attributes.Album.Attributes.Label).attr('disabled', 'disabled');
									if (track.Attributes.Album.Attributes.Compilation != null) {
										if (track.Attributes.Album.Attributes.Compilation) {
											$('#Track_edit_AlbumCompilation').attr('checked', 'checked');
										} else {
											$('#Track_edit_AlbumCompilation').removeAttr('checked');
										}
										$('#Track_edit_AlbumCompilation').attr('disabled', 'disabled');
									}

									if (track.Attributes.Title != null) $('#Track_edit_TrackTitle').val(track.Attributes.Title).attr('disabled', 'disabled');
									if (track.Attributes.Artist != null) $('#Track_edit_TrackArtist').val(track.Attributes.Artist);
									if (track.Attributes.TrackNumber != null) $('#Track_edit_TrackTrackNumber').val(track.Attributes.TrackNumber).attr('disabled', 'disabled');
									if (track.Attributes.Duration != null) $('#Track_edit_TrackDuration').val(Math.floor(parseInt(track.Attributes.Duration) / 60) + ':' + (parseInt(track.Attributes.Duration) % 60 < 10 ? '0' : '') + parseInt(track.Attributes.Duration) % 60).attr('disabled', 'disabled');

									// if ($('#Track_edit_AlbumGenreID').val() != '' && (track.Attributes.Album.Attributes.GenreID != null || (track.Attributes.Album.Attributes.Genre != null && track.Attributes.Album.Attributes.Genre.Attributes.Name != null))) {
									// 	$('#Track_edit_AlbumGenreID').attr('disabled', 'disabled');
									// }

									if (track.Attributes.Album.Attributes.Artist != null || track.Attributes.Artist != null) {
										$('#Track_edit_TrackArtist').attr('disabled', 'disabled');
										$('#Track_edit_AlbumArtist').attr('disabled', 'disabled');
									}
								}

								if ($('#Track_edit_AlbumCompilation').is(':checked')) {
									$('#Track_edit_AlbumArtist').parent().parent().hide();
									$('#Track_edit_TrackArtist').parent().parent().show();
								} else {
									$('#Track_edit_TrackArtist').parent().parent().hide();
									$('#Track_edit_AlbumArtist').parent().parent().show();
								}

								$('#Track_search').hide();
								$('#Track_edit').show();
								$('#Track_preview').hide();
							}

							function Track_show_preview(track) {
								$('#Track_search').hide();
								$('#Track_edit').hide();
								$('#Track_preview').show();
							}

							function Track_validate_edit() {
								// Check all of the fields here
								return true;
							}

							function Track_submit_edit(toSchedule) {
								// if (toSchedule && new Date() < new Date($('#showStartTime').val() * 1000)) {
									// alert('This operation is not available before the show starts.');
									// return false;
								// }

								// Calculate the duration
								var duration = $('#Track_edit_TrackDuration').val();
								var durationSplit = duration.split(':');
								if (durationSplit.length > 1) {
									duration = 60 * parseInt(durationSplit[0]) + parseInt(durationSplit[1]);
								}

								// Require track artist if album is a compilation
								if ($('#Track_edit_AlbumCompilation').is(':checked') && $('#Track_edit_TrackArtist').val() == '') {
									alert('Track Artist is required.');
									return;
								}

								var track = {
									Type: 'Track',
									Attributes: {
										Title: $('#Track_edit_TrackTitle').val(),
										Artist: $('#Track_edit_TrackArtist').val(),
										TrackNumber: $('#Track_edit_TrackTrackNumber').val(),
										Duration: duration,
										Album: {
											Type: 'Album',
											Attributes: {
												Title: $('#Track_edit_AlbumTitle').val(),
												Artist: $('#Track_edit_AlbumArtist').val(),
												Label: $('#Track_edit_AlbumLabel').val(),
												// GenreID: $('#Track_edit_AlbumGenreID').val(),
												Local: $('#Track_edit_AlbumLocal').is(':checked'),
												Compilation: $('#Track_edit_AlbumCompilation').is(':checked'),
												Location: $('#Track_edit_AlbumLocation').val()
											}
										}
									}
								};

								if ($('#Track_edit_AlbumAlbumId').val().length > 0) {
									track.Attributes.AlbumID = track.Attributes.Album.Attributes.AlbumID = $('#Track_edit_AlbumAlbumId').val();
								}

								startLoading();
								Track_submit_addTrackAndTrackPlay(track, toSchedule);
							}

							function Track_submit_trackAndTrackPlay(track, toSchedule) {
								// Try to find the Album
								var albumSearchAttributes = {
									Title: track.Attributes.Album.Attributes.Title
								};

								if (track.Attributes.Album.Attributes.AlbumID) {
									albumSearchAttributes.AlbumID = track.Attributes.Album.Attributes.AlbumID;
								}

								if (!track.Attributes.Album.Attributes.Compilation) {
									albumSearchAttributes.Artist = track.Attributes.Album.Attributes.Artist;
									albumSearchAttributes.Compilation = false;
								} else {
									albumSearchAttributes.Compilation = true;
								}

								dbCommand('find', 'Album', 'MySql', albumSearchAttributes, {}, function(albums) {
									if (albums.length > 0) {
										track.Attributes.AlbumID = albums[0].Attributes.AlbumID;
										track.Attributes.Album = albums[0];
									}

									// Try to find Track
									var trackSearchAttributes = {
										AlbumID: track.Attributes.AlbumID,
										Title: track.Attributes.Title,
										DiskNumber: track.Attributes.DiskNumber,
										TrackNumber: track.Attributes.TrackNumber
									};

									if (track.Attributes.Album.Attributes.Compilation) {
										trackSearchAttributes.Artist = track.Attributes.Artist;
									}

									dbCommand('find', 'Track', 'MySql', trackSearchAttributes, {}, function(foundTracks) {
										var foundTrack;
										if (foundTracks.length > 0) {
											foundTrack = foundTracks[0];
										}

										// Try to find the Genre
										dbCommand('find', 'Genre', 'MySql', (track.Attributes.Album.Attributes.Genre ? track.Attributes.Album.Attributes.Genre.Attributes : { GenreID: track.Attributes.Album.Attributes.GenreID }), {}, function(genres) {
											var needGenre = false; //true; // Never need Genre in this situation any more
											var needLabel = true;
											var needDuration = true;

											if (genres.length > 0) {
												track.Attributes.Album.Attributes.GenreID = genres[0].Attributes.GenreID;
												needGenre = false;
											}
											<?php if ($init->getProp('ReportingPeriod')): ?>
											if (track.Attributes.Album.Attributes.Label && track.Attributes.Album.Attributes.Label.length > 0) {
												needLabel = false;
											}
											if (!foundTrack || foundTrack.Attributes.Duration && foundTrack.Attributes.Duration > 0) {
												needDuration = false;
											}
											<?php else: ?>
											needLabel = false;
											needDuration = false;
											<?php endif; ?>
											// Prompt the user for a Label (if recording period) and a Genre if the Genre is not found
											if (needGenre || needLabel || needDuration) {
												$('#Track_albumMissingInfo').dialog('option', 'buttons', {
													'Cancel': function() {
														$(this).dialog('close');
														endLoading();
													},
													'Ok': function() {
														if (needLabel && $('#Track_albumMissingInfo_AlbumLabel').val() == '') {
															alert('Please enter a valid label');
															return;
														}

														var validDuration = true;
														var duration = $('#Track_albumMissingInfo_TrackDuration').val();
														var durationSplit = duration.split(':');
														if (durationSplit.length > 1) {
															duration = 60 * parseInt(durationSplit[0], 10) + parseInt(durationSplit[1], 10);
														} else {
															validDuration = false;
														}

														if (needDuration && !validDuration) {
															alert('Please enter a valid duration');
															return;
														}

														$(this).dialog('close');
														startLoading();

														if (needGenre) track.Attributes.Album.Attributes.GenreID = $('#Track_albumMissingInfo_AlbumGenreID').val();
														if (needLabel) track.Attributes.Album.Attributes.Label = $('#Track_albumMissingInfo_AlbumLabel').val();
														if (needDuration) track.Attributes.Duration = duration;

														var keepGoing = function() {
															Track_submit_addTrackAndTrackPlay(track, toSchedule, function() {
																$('#Track_albumMissingInfo').dialog('close');
															});
														};

														var saveAlbumAndKeepGoing = function() {
															if (needLabel && track.Attributes.Album.Attributes.AlbumID) {
																dbCommand('save', 'Album', 'MySql', { AlbumID: track.Attributes.Album.Attributes.AlbumID, Label: $('#Track_albumMissingInfo_AlbumLabel').val() }, {}, function() {
																	keepGoing();
																});
															} else {
																keepGoing();
															}
														};

														if (needDuration && track.Attributes.TrackID) {
															dbCommand('save', 'Track', 'MySql', { TrackID: track.Attributes.TrackID, Duration: duration }, {}, function() {
																saveAlbumAndKeepGoing();
															});
														} else {
															saveAlbumAndKeepGoing();
														}
													}
												});

												if (needGenre) {
													$('#Track_albumMissingInfo_AlbumGenreID').val('');

													if (track.Attributes.Album.Attributes.Genre) {
														$('#Track_albumMissingInfo_Genre .iTunesInfo').show();
														$('#Track_albumMissingInfo_Genre .iTunesGenre').html(track.Attributes.Album.Attributes.Genre.Attributes.Name);
													} else {
														$('#Track_albumMissingInfo_Label .iTunesInfo').hide();
													}

													$('#Track_albumMissingInfo_Genre').show();
												} else {
													$('#Track_albumMissingInfo_Genre').hide();
												}

												if (needLabel) {
													$('#Track_albumMissingInfo_AlbumLabel').val('');

													if (track.Attributes.Album.Attributes.Copyright) {
														$('#Track_albumMissingInfo_Label .iTunesInfo').show();
														$('#Track_albumMissingInfo_Label .iTunesCopyright').html(track.Attributes.Album.Attributes.Copyright);
														delete(track.Attributes.Album.Attributes.Copyright);
													} else {
														$('#Track_albumMissingInfo_Label .iTunesInfo').hide();
													}

													$('#Track_albumMissingInfo_Label').show();
												} else {
													$('#Track_albumMissingInfo_Label').hide();
												}

												if (needDuration) {
													$('#Track_albumMissingInfo_TrackDuration').val('');

													$('#Track_albumMissingInfo_Duration').show();
												} else {
													$('#Track_albumMissingInfo_Duration').hide();
												}

												$('#Track_albumMissingInfo').dialog('open');
												endLoading();
											} else {
												Track_submit_addTrackAndTrackPlay(track, toSchedule);
											}
										});
									});
								});
							}

							function Track_submit_addTrackAndTrackPlay(track, toSchedule, callback) {
								if (track.Attributes.TrackID) {
									Track_submit_trackPlay(track.Attributes.TrackID, toSchedule, callback);
									return;
								}

								var albumSearchAttributes = {
									Title: track.Attributes.Album.Attributes.Title
								};

								if (!track.Attributes.Album.Attributes.Compilation) {
									albumSearchAttributes.Artist = track.Attributes.Album.Attributes.Artist;
									albumSearchAttributes.Compilation = false;
								} else {
									albumSearchAttributes.Compilation = true;
								}

								// Find or create the Album:
								findOrInsertDifferentDBObjects('Album', 'MySql', albumSearchAttributes, $.extend({'AddDate': 'NOW'}, track.Attributes.Album.Attributes), function(albumResponse) {
									if (albumResponse && !albumResponse.error) {
										var albumId = (albumResponse.AlbumID ? albumResponse.AlbumID : albumResponse.Attributes.AlbumID);

										track.Attributes.AlbumID = albumId;

										var trackSearchAttributes = {
											AlbumID: track.Attributes.AlbumID,
											Title: track.Attributes.Title,
											DiskNumber: track.Attributes.DiskNumber,
											TrackNumber: track.Attributes.TrackNumber
										};

										if (track.Attributes.Album.Attributes.Compilation) {
											trackSearchAttributes.Artist = track.Attributes.Artist;
										}

										// Find or create the Track
										findOrInsertDifferentDBObjects('Track', 'MySql', trackSearchAttributes,  track.Attributes, function(trackResponse) {
											if (trackResponse && !trackResponse.error) {
												var trackId = (trackResponse.TrackID ? trackResponse.TrackID : trackResponse.Attributes.TrackID);

												// Add the TrackPlay
												Track_submit_trackPlay(trackId, toSchedule, callback);
											} else {
												alert('Error adding Track.  Check required fields and try again.');
												endLoading();
											}
										});
									} else {
										alert('Error adding Album.  Check required fields and try again.');
										endLoading();
									}
								});
							}

							function Track_submit_trackPlay(trackId, toSchedule, callback) {
								var startDateTime = new Date($('#showEndTime').val() * 1000).format('yyyy-mm-dd HH:MM:ss');

								var trackPlayAttributes = {
									TrackId: trackId,
									ScheduledShowInstanceId: $('#showId').val(),
									StartDateTime: startDateTime
								};

								if (toSchedule) trackPlayAttributes['Executed'] = parseInt($('#showEndTime').val());

								dbCommand('insert', 'TrackPlay', 'MySql', trackPlayAttributes, {}, function(trackPlayResponse) {
									if (trackPlayResponse && !trackPlayResponse.error) {
										$('#Track_search_keywords').val('');
										$('#Track_search_results').empty();
										Track_show_search();
										refreshScratchpadAndSchedule(function() {
											var scratchpadDone = false;
											var scheduleDone = false;

											updateFloatingEventTimes(function() {
												if (callback) callback();
												endLoading();
											});
										});
									} else {
										alert('Error adding Track.')
										endLoading();
									}
								});
							}
						</script>
						<div id="Track_search">
							<form onsubmit="Track_submit_search(); return false;">
								<label for="Track_search_keywords" style="width: 90px; float: left; margin-top: 4px; display: block">Keyword Search:</label>
								<input class="disableWhenLoading" type="submit" style="float: right; margin-top: 2px" value="Go">
								<div style="margin-left: 92px; margin-right: 40px">
									<input id="Track_search_keywords" type="text" style="width: 100%">
								</div>
							</form>
							<div id="Track_search_results"></div>
						</div>
						<div id="Track_edit">
							<div id="Track_edit_Track">
								<h4>Track Information</h4>
								<div class="field"><label for="Track_edit_TrackTitle">Track Title<span style="color: red">*</span></label><div class="input"><input type="text" id="Track_edit_TrackTitle"></div></div>
								<div class="field"><label for="Track_edit_TrackArtist">Artist<span style="color: red">*</span></label><div class="input"><input type="text" id="Track_edit_TrackArtist"></div></div>
								<div class="field"><label for="Track_edit_TrackTrackNumber">Track Number<span style="color: red">*</span></label><div class="input"><input type="text" id="Track_edit_TrackTrackNumber"></div></div>
								<div class="field"><label for="Track_edit_TrackDuration">Duration<?php if ($init->getProp('ReportingPeriod')) echo '<span style="color: red">*</span>'; ?></label><div class="input"><input type="text" id="Track_edit_TrackDuration"></div></div>
							</div>
							<div id="Track_edit_Album">
								<h4>Album Information</h4>
								<div class="field"><label for="Track_edit_AlbumSearch">Album Title<span style="color: red">*</span></label><div class="input"><input type="text" id="Track_edit_AlbumSearch"></div></div>
								<div class="album_fields">
									<div class="field"><label for="Track_edit_AlbumTitle">Album Title<span style="color: red">*</span></label><div class="input"><input type="text" id="Track_edit_AlbumTitle"></div></div>
									<div class="field"><label for="Track_edit_AlbumArtist">Artist<span style="color: red">*</span></label><div class="input"><input type="text" id="Track_edit_AlbumArtist"></div></div>
									<div class="field"><label for="Track_edit_AlbumLabel">Label<?php if ($init->getProp('ReportingPeriod')) echo '<span style="color: red">*</span>'; ?></label><div class="input"><input type="text" id="Track_edit_AlbumLabel"></div></div>
									<!--<div class="field"><label for="Track_edit_AlbumGenreID">Genre</label><div class="input">
										<select id="Track_edit_AlbumGenreID">
											<option value="">Select a Genre</option>
<?php foreach (DB::getInstance('MySql')->find(new Genre(array('TopLevel' => true)), $count,  array('sortcolumn' => 'Name', 'limit' => false)) as $genre): ?>
											<option value="<?php echo $genre->GenreID ?>"><?php echo $genre->Name ?></option>
<?php endforeach; ?>
										</select>
									</div></div>-->
									<div class="field"><label for="Track_edit_AlbumCompilation">Compilation:</label><div class="input"><input type="checkbox" id="Track_edit_AlbumCompilation"></div></div>
								</div>
								<input type="hidden" id="Track_edit_AlbumAlbumId" value="">
							</div>
							<input id="Track_edit_AlbumLocation" type="hidden" value="Personal">
							<div style="padding-top: 20px; clear: both">
						    	<button class="disableWhenLoading" style="font-weight: bold" onclick="Track_submit_edit()">Add to Scratchpad</button>
								<button class="disableWhenLoading" onclick="Track_submit_edit(true)">Add to Saved Items</button>
								<button class="disableWhenLoading" onclick="Track_show_search()">Cancel</button>
							</div>
						</div>
						<div id="Track_albumMissingInfo" class="dialog">
							<div id="Track_albumMissingInfo_Label">
								<div class="field">This Album is missing Label information, required for the current reporting period.</div>
								<div class="field iTunesInfo">iTunes lists the copyright information for this album as: <div class="iTunesCopyright"></div></div>
								<div class="field"><label for="Track_albumMissingInfo_AlbumLabel">Label:</label><div class="input"><input type="text" id="Track_albumMissingInfo_AlbumLabel"></div></div>
							</div>
							<div id="Track_albumMissingInfo_Duration">
								<div class="field">This Track is missing duration information, required for the current reporting period.</div>
								<div class="field"><label for="Track_albumMissingInfo_TrackDuration">Duration:</label><div class="input"><input type="text" id="Track_albumMissingInfo_TrackDuration" placeholder="m:ss"></div></div>
							</div>
							<div id="Track_albumMissingInfo_Genre">
								<div class="field iTunesInfo">iTunes lists this album under the <span class="iTunesGenre"></span> genre, but that genre does not exist in the KGNU catalog.</div>
								<div class="field">Please select an appropriate KGNU genre from the list below.</div>
								<div class="field"><label for="Track_albumMissingInfo_AlbumGenreID">Genre:</label><div class="input">
									<select id="Track_albumMissingInfo_AlbumGenreID">
										<option value="">Select a Genre</option>
<?php foreach (DB::getInstance('MySql')->find(new Genre(array('TopLevel' => true)), $count,  array('sortcolumn' => 'Name', 'limit' => false)) as $genre): ?>
										<option value="<?php echo $genre->GenreID ?>"><?php echo $genre->Name ?></option>
<?php endforeach; ?>
									</select>
								</div></div>
							</div>
						</div>
					</div>

					<div id="PSA_tab">
						<script type="text/javascript">
							$(function() {
								PSA_update();
							});

							function PSA_submit(toSchedule) {
								// if (toSchedule && new Date() < new Date($('#showStartTime').val() * 1000)) {
									// alert('This operation is not available before the show starts.');
									// return false;
								// }

								if (parseInt($('#PSA_tab_search_title').val()) > 0) {
									var attributes = {
										ScheduledShowInstanceId: $('#showId').val(),
										StartDateTime: new Date($('#showEndTime').val() * 1000).format('yyyy-mm-dd HH:MM:ss'),
										EventId: $('#PSA_tab_search_title').val()
									};

									if (toSchedule) attributes['Executed'] = parseInt($('#showEndTime').val());

									startLoading();
									dbCommand('save', 'FloatingShowEvent', 'MySql', attributes, {}, function(response) {
										if (response && !response.error) {
											refreshScratchpadAndSchedule(function() {
												var scratchpadDone = false;
												var scheduleDone = false;

												updateFloatingEventTimes(function() {
													endLoading();
													$('#PSA_tab_search_category').val('').change();
												});
											});
										}
									});
								}
							}

							function PSA_update() {
								if ($('#PSA_tab_search_category').val() == '') {
									$('#PSA_tab_search_title').html('');
									return;
								}

								var criteria = [['Active', '=', true], ['StartDate', '<=', new Date().format('yyyy-mm-dd')], ['KillDate', '>=', new Date().format('yyyy-mm-dd')]];
								if ($('#PSA_tab_search_category').val() != 'All') criteria.push(['PSACategoryId', '=', $('#PSA_tab_search_category').val()]);
								dbCommandCriteria('find', 'PSAEvent', 'MySql', criteria, { sortcolumn: 'Title' }, function(results) {
									$('#PSA_tab_search_title').empty();
									$.each(results, function(i, psa) {
										$('#PSA_tab_search_title').append('<option value="' + psa.Attributes.Id + '">' + psa.Attributes.Title + '</option>');
									});
								});
							}
						</script>
						<div id="PSA_tab_search">
							<div style="margin-top: 10px">
								<label for="PSA_tab_search_category">Category:</label>
								<select id="PSA_tab_search_category" onchange="PSA_update()">
									<option value="" selected="selected">Select a Category</option>
									<option value="All">All</option>
									<?php $categories = DB::getInstance('MySql')->find(new PSACategory(), $count, array('sortcolumn' => 'Title')); ?>
									<?php foreach ($categories as $category): ?>
										<option value="<?php echo $category->Id ?>"><?php echo $category->Title ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div style="margin-top: 10px">
								<label for="PSA_tab_search_title">PSA:</label>
								<select id="PSA_tab_search_title"></select>
							</div>
							<div style="margin-top: 20px">
						    	<button class="disableWhenLoading" style="font-weight: bold" onclick="PSA_submit()">Add to Scratchpad</button>
								<button class="disableWhenLoading" onclick="PSA_submit(true)">Add to Saved Items</button>
							</div>
						</div>
					</div>
<!-- Removing Ticket Giveaways for now
					<div id="TicketGiveaway_tab">
						<script type="text/javascript">
							function TicketGiveaway_submit(toSchedule) {
								if ($('#TicketGiveaway_tab_search_title').val() == '') return;

								if (toSchedule && new Date() < new Date($('#showStartTime').val() * 1000)) {
									alert('This operation is not available before the show starts.');
									return false;
								}

								if (parseInt($('#TicketGiveaway_tab_search_title').val()) > 0) {
									var attributes = {
										ScheduledShowInstanceId: $('#showId').val(),
										StartDateTime: new Date($('#showEndTime').val() * 1000).format('yyyy-mm-dd HH:MM:ss'),
										EventId: $('#TicketGiveaway_tab_search_title').val()
									};

									if (toSchedule) attributes['Executed'] = $('#showEndTime').val();

									startLoading();
									dbCommand('save', 'FloatingShowEvent', 'MySql', attributes, {}, function(response) {
										if (response && !response.error) {
											refreshScratchpadAndSchedule(function() {
												updateFloatingEventTimes(function() {
													$('#TicketGiveaway_tab_search_title').val('');
													endLoading();
												});
											});
										}
									});
								}
							}
						</script>
						<div id="TicketGiveaway_tab_search">
							<div style="margin-top: 10px">
								<label for="TicketGiveaway_tab_search_title">Ticket Giveaway: </label>
								<select id="TicketGiveaway_tab_search_title">
									<option value="" selected="selected">Select a Ticket Giveaway</option>
									<?php $ticketGiveaways = DB::getInstance('MySql')->find(new TicketGiveawayEvent(array('Active' => true)), $count, array('sortcolumn' => 'Title')); ?>
									<?php foreach ($ticketGiveaways as $ticketGiveaway): ?>
										<option value="<?php echo $ticketGiveaway->Id ?>"><?php echo $ticketGiveaway->Title ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div style="margin-top: 20px">
						    	<button class="disableWhenLoading" style="font-weight: bold" onclick="TicketGiveaway_submit()">Add to Scratchpad</button>
								<button class="disableWhenLoading" onclick="TicketGiveaway_submit(true)">Add to Saved Items</button>
							</div>
						</div>
					</div>
-->
					<div id="DJComment_tab">
						<script type="text/javascript">
							function DJComment_submit(toSchedule) {
								// if (toSchedule && new Date() < new Date($('#showStartTime').val() * 1000)) {
									// alert('This operation is not available before the show starts.');
									// return false;
								// }

								var attributes = {
									ScheduledShowInstanceId: $('#showId').val(),
									StartDateTime: new Date($('#showEndTime').val() * 1000).format('yyyy-mm-dd HH:MM:ss'),
									Body: $('#DJComment_tab_body').val()
								};

								if (toSchedule) attributes['Executed'] = parseInt($('#showEndTime').val());

								startLoading();
								dbCommand('save', 'DJComment', 'MySql', attributes, {}, function(response) {
									if (response && !response.error) {
										refreshScratchpadAndSchedule(function() {
											var scratchpadDone = false;
											var scheduleDone = false;

											updateFloatingEventTimes(function() {
												$('#DJComment_tab_body').val('');
												endLoading();
											});
										});
									} else {
										if (response.error) alert(response.error);
										endLoading();
									}
								});
							}
						</script>
						<div id="DJComment_tab_search">
							<textarea id="DJComment_tab_body" class="tinymce" style="width: 100%; height:200px"></textarea>
							<div style="margin-top: 10px">
						    	<button class="disableWhenLoading" style="font-weight: bold" onclick="DJComment_submit()">Add to Scratchpad</button>
								<button class="disableWhenLoading" onclick="DJComment_submit(true)">Add to Saved Items</button>
							</div>
						</div>
					</div>
					<div id="VoiceBreak_tab">
						<script type="text/javascript">
							function VoiceBreak_submitVoiceBreak(toSchedule) {
								// if (toSchedule && new Date() < new Date($('#showStartTime').val() * 1000)) {
									// alert('This operation is not available before the show starts.');
									// return false;
								// }

								var attributes = {
									ScheduledShowInstanceId: $('#showId').val(),
									StartDateTime: new Date($('#showEndTime').val() * 1000).format('yyyy-mm-dd HH:MM:ss')
								};

								if (toSchedule) attributes['Executed'] = parseInt($('#showEndTime').val());

								startLoading();
								dbCommand('save', 'VoiceBreak', 'MySql', attributes, {}, function(response) {
									if (response && !response.error) {
										refreshScratchpadAndSchedule(function() {
											updateFloatingEventTimes(function() {
												endLoading();
											});
										});
									}
								});
							}
						</script>
						<div>
					    	<button class="disableWhenLoading" style="font-weight: bold" onclick="VoiceBreak_submitVoiceBreak()">Add to Scratchpad</button>
							<button class="disableWhenLoading" onclick="VoiceBreak_submitVoiceBreak(true)">Add to Saved Items</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="right">
		<div id="schedule">
			<h5>Saved Items
				<button style="float: right;" onclick="$('#schedule_list li.TrackPlay ul.eventDetails').hide();">Contract Tracks</button>
				<button style="float: right;" onclick="$('#schedule_list li.TrackPlay ul.eventDetails').show();">Expand Tracks</button>
			</h5>
			<ul id="schedule_list">
			</ul>
		</div>
	</div>

	<input id="showId" type="hidden" value="<?php echo $scheduledShowInstance->Id ?>">
	<input id="showStartTime" type="hidden" value="<?php echo $scheduledShowInstance->StartDateTime ?>">
	<input id="showEndTime" type="hidden" value="<?php echo $scheduledShowInstance->StartDateTime + $scheduledShowInstance->Duration * 60 ?>">

	<div id="EditShowDescription" class="dialog">
		<div style="padding: 0px 20px; border: 1px solid #CCC; background-color: white;">
			<script type="text/javascript">
				function initEditShowDescriptionDialog() {
					// Init the dialog functionality
					$('#EditShowDescription').dialog({
						autoOpen: false,
						modal: true,
						closeOnEscape: true,
						resizable: false,
						width: 600,
						position: 'top',
						title: 'Edit today\'s show description',
						open: function(event, id) {
							initRichTextFieldsFor($('#EditShowDescription'));
						},
						beforeclose: function(event, ui) {
							if (kgnuTinyMCEInitialized) removeRichTextFieldsFor($('#EditShowDescription'));
						},
						buttons: {
							'Save': function() {
								startLoading();
								var _this = this;
								dbCommand('save', 'ScheduledShowInstance', 'MySql', {
									Id: $('#showId').val(),
									ShortDescription: $('#EditShowDescription_ShortDescription').val(),
									LongDescription: $('#EditShowDescription_LongDescription').val()
								}, {}, function(response) {
									endLoading();
									$(_this).dialog('close');
								});
							},
							'Cancel': function() {
								$(this).dialog('close');
							}
						}
					});
				}

				function showEditShowDescriptionDialog() {
					startLoading();
					dbCommand('find', 'ScheduledShowInstance', 'MySql', { Id: $('#showId').val() }, {}, function(sei) {
						if (sei.length > 0) {
							dbCommand('find', 'ScheduledEvent', 'MySql', { Id: sei[0].Attributes.ScheduledEventId }, {}, function(se) {
								if (se.length > 0) {
									dbCommand('get', 'Event', 'MySql', { Id: se[0].Attributes.EventId }, {}, function(e) {
										var shortDescription = (sei[0].Attributes.ShortDescription !== null ? sei[0].Attributes.ShortDescription : e.Attributes.ShortDescription);
										var longDescription = (sei[0].Attributes.LongDescription !== null ? sei[0].Attributes.LongDescription : e.Attributes.LongDescription);
										$('#EditShowDescription_ShortDescription').val(shortDescription);
										$('#EditShowDescription_LongDescription').val(longDescription);
										$('#EditShowDescription').dialog('open');
										endLoading();
									});
								}
							});
						}
					});

					return false;
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

				$(function() {
					initEditShowDescriptionDialog();
				});
        
        window.onerror = function(message, url, lineNumber) {  
          alert('Show Builder Error: ' + message + '(' + lineNumber + ')');
          //save error and send to server for example.
          return false;
        };  
			</script>
			<div style="margin: 20px 0">
				<label for="EditShowDescription_ShortDescription" style="display: block; font-size: 1.2em; font-weight: bold;">Short Description:</label>
				<input id="EditShowDescription_ShortDescription" type="text" style="display: block; width: 100%;"></input>
			</div>
			<div style="margin: 20px 0">
				<label for="EditShowDescription_LongDescription" style="display: block; font-size: 1.2em; font-weight: bold;">Long Description:</label>
				<textarea id="EditShowDescription_LongDescription" class="tinymce"></textarea>
			</div>
		</div>
	</div>


<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();								   # ?>
<?php $close->write();													   # ?>
<?php ###################################################################### ?>
