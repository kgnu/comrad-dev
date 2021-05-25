<?php

	require_once('initialize.php'); 

	$eventTypes = array(
		'AlertEvent' => 'Alert',
		'AnnouncementEvent' => 'Announcement',
		'EASTestEvent' => 'EAS Test',
		'FeatureEvent' => 'Feature',
		'TicketGiveawayEvent' => 'Giveaway',
		'Host' => 'Host',
		'LegalIdEvent' => 'Legal ID',
		'PSAEvent' => 'PSA',
		'ShowEvent' => 'Show',
		'UnderwritingEvent' => 'Underwriting',
		'Venue' => 'Venue'
	);
	
?>

<?php ###################################################################### ?>
<?php $head=new HeadTemplateSection();                                     # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Manage Events</title>

	<link type="text/css" rel="stylesheet" href="css/jquery/autocomplete/jquery.autocomplete.css" />
	<link type="text/css" rel="stylesheet" href="css/jquery/jgrowl/jquery.jgrowl.css" />
	<style type="text/css">
		label { display: block; font-size: 1.2em; font-weight: bold }
		.field { width: 400px; margin: 35px 30px }
		.inputField { width: 100% }
		.required { color: #c33 }
		form p { margin: 0px; padding: 0px }
	</style>
	
	<script type="text/javascript" src="js/jquery/ajaxqueue/jquery.ajaxqueue.js"></script>
	<script type="text/javascript" src="js/jquery/bgiframe/jquery.bgiframe.js"></script>
	<script type="text/javascript" src="js/jquery/dimensions/jquery.dimensions.js"></script>
	<script type="text/javascript" src="js/jquery/thickbox/thickbox.js"></script>
	<script type="text/javascript" src="js/jquery/autocomplete/jquery.autocomplete.js"></script>
	
	<script type="text/javascript" src="js/jquery/json/jquery.json.js"></script>
	<script type="text/javascript" src="js/jquery/jgrowl/jquery.jgrowl.js"></script>
	
	<script type="text/javascript" src="js/jquery/tinymce/tinymce/jquery.tinymce.js"></script>
	<script type="text/javascript" src="js/tinymce/kgnutinymce.js"></script>
	
	<script type="text/javascript" src="js/ajax/ajaxdbinterface.js"></script>

	<script type="text/javascript">
		var pendingEdits = false;
	 	
		function updateEventType() {
			resetEventTypes();
			
			pendingEdits = true;
		}

		function resetEventTypes() {
			$('.eventForm').hide();
			clearForms();
			pendingEdits = false;
		}
		
		function clearForms() {
		    $('.eventForm :input').each(function() {
				var inputType = this.type;
				var inputTag = this.tagName.toLowerCase();
				var value = '';
				if ($(this).is("[defaultvalue]") != '') {
					value = $(this).attr("defaultvalue");
				}
				if (inputType == 'text' || inputType == 'password' || inputType == 'hidden') {
					this.value = value;
				} else if (inputType == 'checkbox' || inputType == 'radio') {
					this.checked = false;
				} else if (inputTag == 'textarea') {
				    $(this).val(value);
				} else if (inputTag == 'select') {
					this.selectedIndex = -1;
				}
			});
		}
	
		function saveEvent(type) {
			var attributes = {};
			var selected = $(':input', '#' + type + 'Form');
			var lastSelectedEvent;
			
			//custom validation: validate the ShowName, ShowDate and Venue for Giveaways
			if (type == 'TicketGiveawayEvent') {
				var ticketType = $("#TicketGiveawayEventTicketType").val();
				if (ticketType == 'Paper Ticket' || ticketType == 'Guest List Ticket' || ticketType == 'Digital Ticket') {
					//we require ShowName, ShowDate and Venue because they are used in the winner's email template
					if (!$("#TicketGiveawayEventShowName").val() || !$("#TicketGiveawayEventShowDate").val() || !$("#TicketGiveawayEventVenue").val()) {
						$.jGrowl('You must provide a Show Name, Show Date and Venue for giveaways with Paper or Guest List Tickets.', {
							header: 'Error',
							life: 10000,
							glue: 'before'
						});
						return false;
					}
				}
			}
			
			$(':input', '#' + type + 'Form').each(function() {
				if (this.name.substr(0, type.length) == type) {
					var inputType = this.type;
					var inputTag = this.tagName.toLowerCase();
					if (inputType == 'text' || inputType == 'password' || inputType == 'hidden') {
						attributes[this.id.substr(type.length)] = this.value;
					} else if (inputType == 'checkbox') {
						attributes[this.id.substr(type.length)] = this.checked;
					} else if (inputType == 'radio') {
						if (this.checked) attributes[this.id.substr(type.length)] = this.value;
					} else if (inputTag == 'textarea' || inputTag == 'select') {
						attributes[this.id.substr(type.length)] = $(this).val();
					}
				}
			});
			
			startLoading();
			dbCommand('save', type, 'MySql', attributes, {}, function(results) {
				if (results.error) {
					$.jGrowl('Check to make sure you have filled all the required fields and try again.  Required fields are marked by a red asterisk (*).', {
						header: 'Error',
						life: 10000,
						glue: 'before'
					});
				} else {
					$.jGrowl('The object was successfully saved.', {
						header: 'Success',
						life: 10000,
						glue: 'before'
					});
					$('#eventSearch').val('');
					resetEventTypes();
				}
				endLoading();
			});
		
			return false;
		}
		
		function initFields() {
			$('.datePickerInput').datepicker({dateFormat: 'm/d/yy', showButtonPanel: true, changeMonth: true, changeYear: true});
			
			initializeKGNUTinyMCEForSelector($('textarea.tinymce'));
		}
		
		function changeEventSelect(e) {
			if (!okayToAbandonChanges()) {
				$('#EventSelect').val(lastSelectedEvent);
				return;
			}
			
			resetEventTypes();
			
			$('#eventSearch').attr('disabled', ($('#EventSelect').val() == '-1'));
			
			if (!$('#eventSearch').attr('disabled')) {
				$('#eventSearch').val('');
				$('#eventSearch').focus();
			}
			
			lastSelectedEvent = $('#EventSelect').val();
		}
		
		function okayToAbandonChanges() {
			return (!pendingEdits || (pendingEdits && confirm('Are you sure you want to abandon any changes made to this Event?')));
		}
		
		function showAttributesFor(event) {
			for (var key in event.Attributes) {
				var element = $('#' + event.Type + key);
				if (element.length > 0) {
					var elementType = element[0].type;
					var elementTag = element[0].tagName.toLowerCase();
					if (element.hasClass('datePickerInput')) {
						if (event.Attributes[key]) {
							var d = new Date(event.Attributes[key] * 1000);
							element[0].value = (d.getMonth() + 1) + '/' + (d.getDate() + 1) + '/' + d.getFullYear();
						}
					} else if (element.hasClass('autocomplete')) {
						element[0].value = event.Attributes[key];
						element.change();
					} else if (elementType == 'text' || elementType == 'password' || elementType == 'hidden') {
						element[0].value = event.Attributes[key];
					} else if (elementType == 'checkbox') {
						element[0].checked = event.Attributes[key];
					} else if (elementType == 'radio') {
						$('#' + event.Type + key + '[value="' + event.Attributes[key] + '"]').attr('checked', true);
					} else if (elementTag == 'textarea' || elementTag == 'select') {
						element.val(event.Attributes[key]);
					}
				}
			}

			$('#' + event.Type + 'Form').show();
            // ScheduleEvent_initAttributeFields(event.Type);
			$('.titleColumn:visible').focus();
		}
		
		function startLoading() {
			$('input').attr('disabled', true);
			$('select').attr('disabled', true);
			$('textarea').attr('disabled', true);
			$('#loading').text('Loading...');
		}
		
		function endLoading() {
			$('#loading').text('');
			$('input').removeAttr('disabled');
			$('select').removeAttr('disabled');
			$('textarea').removeAttr('disabled');
		}
	
		$(function () {
			var eventSelect = $('#EventSelect');
			
			window.onbeforeunload = function() {
				if (pendingEdits) return "There may be unsaved changes that will be lost if you continue.";
			};
		
			$('#eventSearch').autocomplete('ajax/autocomplete/events.php', {
				minChars: 0,
				cacheLength: 0,
                extraParams: {
                    allowcreatenew: true,
                    showall: true,
                    type: function() { return eventSelect.val(); }
                },
				max: 0,
				mustMatch: false,
				matchSubset: false,
				delay: 10
			}).result(function(event, item) {
			    if (item && item.length == 2) {
					var event = $.evalJSON(item[1]);
					$('#eventSearch').val('');
                    
    				if (!okayToAbandonChanges()) return;
				    
					updateEventType();
					showAttributesFor(event);
				}
			});
			
			initFields();
            
			resetEventTypes();
		});
		
		$(function() {
			$("#TicketGiveawayEventShowName").data("token", "[ShowName]");
			$("#TicketGiveawayEventShowDate").data("token", "[ShowDate]");
			$("#TicketGiveawayEventVenue").data("token", "[Venue]");
			$("#TicketGiveawayEventTicketQuantity").data("token", "[TicketQuantity]");
			
			var $fieldsWithTokens = $("#TicketGiveawayEventShowName,#TicketGiveawayEventShowDate,#TicketGiveawayEventVenue,#TicketGiveawayEventTicketQuantity");
			$fieldsWithTokens.change(function() {
					$fieldsWithTokens.each(function() {
					if (jQuery.trim($(this).val()).length == 0) return;
					
					var value = $(this).val();
					var token = $(this).data("token");
					$("#TicketGiveawayEventCopy").val($("#TicketGiveawayEventCopy").val().replace(token, value));
					$(this).data("token", value);
				});
			});
		});
	</script>

<?php ###################################################################### ?>
<?php $body=new BodyTemplateSection();                                     # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>

	<h4>Manage Events</h4>

	<fieldset>
		<legend>
			<select id="EventSelect" onChange="changeEventSelect()">
				<option value="-1">Please select an event type...</option>
				<?php foreach($eventTypes as $eventType => $displayString): ?>
					<option value="<?php echo $eventType ?>"><?php echo $displayString ?></option>
				<?php endforeach; ?>
			</select>
			<input type="text" id="eventSearch" name="eventSearch" disabled="true" style="width: 400px">
			<span id="loading"></span>
		</legend>
		
		<?php foreach ($eventTypes as $eventType => $displayString): ?>
			<?php $event = new $eventType(); $titleColumnName = $event->getTitleColumn(); $primaryKey = $event->getPrimaryKey() ?>
			<form id="<?php echo $eventType ?>Form" class="eventForm" action="events.php" onsubmit="return saveEvent('<?php echo $eventType ?>');" style="display: none">
				<div id="<?php echo $eventType ?>">
					<?php $event = new $eventType(); ?>
					<?php foreach ($event->getColumns() as $columnName => $column): ?>
						<?php if ($column['type'] == 'ForeignKeyItem' || $column['type'] == 'ForeignKeyCollection' || (array_key_exists('showinform', $column) && $column['showinform'] == false)) continue; ?>
						<div class="field">
							<?php if ($column['type'] != 'PrimaryKey'): ?>
								<label for="<?php echo $eventType.$columnName ?>"><?php echo (array_key_exists('tostring', $column) ? $column['tostring'] : $columnName) ?><?php if ($event->isRequiredField($columnName) && $column['type'] != 'Boolean') echo '<span class="required">*</span>' ?></label>
							<?php endif; ?>
							<?php switch ($column['type']) { 
								
								case 'PrimaryKey': ?>
								    <input type="hidden" id="<?php echo $eventType.$columnName ?>" name="<?php echo $eventType.$columnName ?>">
								<?php break;
								
								case 'ForeignKey': ?>
									<input type="text" name="<?php echo $eventType.$columnName ?>_autocomplete" id="<?php echo $eventType.$columnName ?>_autocomplete" class="inputField">
									<input type="hidden" id="<?php echo $eventType.$columnName ?>" name="<?php echo $eventType.$columnName ?>" class="autocomplete">
									<?php $foreignObject = new $column['foreignType'](); ?>
									<script type="text/javascript" charset="utf-8">
										$('#<?php echo $eventType.$columnName ?>_autocomplete').autocomplete('ajax/autocomplete/events.php', {
											minChars: 0,
											cacheLength: 0,
											extraParams: {
												type: '<?php echo $column['foreignType'] ?>',
												allownone: true
											},
											mustMatch: true,
											matchSubset: false,
											delay: 10
										}).result(function(event, item) {
											if (item && item.length == 2) {
												if (item[1] == '') {
													$('#<?php echo $eventType.$columnName ?>').val('0');
													$('#<?php echo $eventType.$columnName ?>_autocomplete').val('None');
												} else {
													var event = $.evalJSON(item[1]);
													$('#<?php echo $eventType.$columnName ?>').val(event.Attributes.<?php echo $foreignObject->getPrimaryKey() ?>);
												}
											}
										});
										
										$(function() {
											$('#<?php echo $eventType.$columnName ?>').change(function(eventObject) {
												if ($(this).val() > 0) {
													dbCommand('get', '<?php echo $column['foreignType'] ?>', 'MySql', { <?php echo $foreignObject->getPrimaryKey() ?>: $(this).val() }, {}, function(object) {
														$('#<?php echo $eventType.$columnName ?>_autocomplete').val(object.Attributes.<?php echo $foreignObject->getTitleColumn() ?>);
													});
												}
											});
										});
									</script>
								<?php break;
								
								case 'String': ?>
									<textarea name="<?php echo $eventType.$columnName ?>" id="<?php echo $eventType.$columnName ?>" class="inputField tinymce<?php if($titleColumnName == $columnName) echo ' titleColumn' ?>" <?php if (isset($column['defaultvalue'])) echo 'defaultvalue="' . str_replace('"', '\"', htmlentities(str_replace("\r\n", "", $column['defaultvalue']))) . '"'; ?>></textarea>
								<?php break;
								
								case 'ShortString':
								case 'UppercaseString': ?>
									<input type="text" name="<?php echo $eventType.$columnName ?>" id="<?php echo $eventType.$columnName ?>" class="inputField<?php if($titleColumnName == $columnName) echo ' titleColumn' ?>">
								<?php break;
								
								case 'Enumeration': ?>
									<?php foreach($column['possiblevalues'] as $possibleValue): ?>
										<p><input type="radio" id="<?php echo $eventType.$columnName ?>" name="<?php echo $eventType.$columnName ?>" value="<?php echo $possibleValue ?>">
											<?php echo $possibleValue ?>
										</p>
									<?php endforeach; ?>
								<?php break;
								
								case 'Boolean': ?>
									<input type="checkbox" id="<?php echo $eventType.$columnName ?>" name="<?php echo $eventType.$columnName ?>">
								<?php break;
								
								case 'Integer': ?>
									<input type="text" id="<?php echo $eventType.$columnName ?>" name="<?php echo $eventType.$columnName ?>" class="inputField">
								<?php break;
								
								case 'Date': ?>
									<input type="text" id="<?php echo $eventType.$columnName ?>" name="<?php echo $eventType.$columnName ?>" value="" class="inputField datePickerInput">
								<?php break; ?>
								
							<?php } ?>
						</div>
					<?php endforeach; ?>
					<input type="hidden" name="type" value="<?php echo $eventType ?>">
					<input type="submit" id="saveButton" value="Save">
					<input type="button" id="cancelButton" value="Cancel" onclick="if (okayToAbandonChanges()) { $('#eventSearch').val(''); $('.eventForm').hide(); pendingEdits = false; }">
				</div>
			</form>
		<?php endforeach; ?>
	</fieldset>
	
<?php ###################################################################### ?>
<?php $close=new CloseTemplateSection();                                   # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
