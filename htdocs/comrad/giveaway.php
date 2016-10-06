<?php

	require_once('initialize.php'); 
	
	$seiid = $_GET['seiid'];
	
	$results = DB::getInstance('MySql')->find(new ScheduledTicketGiveawayInstance(array('Id' => $seiid)));
	$instance = $results[0];
	
	$winnerName = $instance->WinnerName;
	if ($instance->NoCallers || ! empty($winnerName)) {
		$disabled = TRUE;
	}
?>

<?php ###################################################################### ?>
<?php $head=new HeadTemplateSection();                                     # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Enter Giveaway Information</title>

	<link type="text/css" rel="stylesheet" href="css/jquery/jgrowl/jquery.jgrowl.css" />
	<style type="text/css">
		label { display: block; font-size: 1.2em; font-weight: bold }
		.field { width: 400px; margin: 35px 30px }
		.inputField { width: 100% }
		.required { color: #c33 }
		form p { margin: 0px; padding: 0px }
	</style>
	
	<script type="text/javascript" src="js/jquery/json/jquery.json.js"></script>
	<script type="text/javascript" src="js/jquery/jgrowl/jquery.jgrowl.js"></script>
	
	<script type="text/javascript" src="js/ajax/ajaxdbinterface.js"></script>

	<script type="text/javascript">
		var pendingEdits = false;
		
		$(function() {
			$("form").submit(function(e) {
				e.preventDefault();
				
				<?php if ($disabled) { ?> return; <?php } ?>
				
				//validation
				var valid = false;
				if ($("[name=ScheduledTicketGiveawayEventInstanceNoCallers]").is(":checked")) {
					valid = true;
					$("input[type='text']").each(function() {
						if ($.trim($(this).val()).length > 0) {
							valid = false;
						}
					});
					if ( ! valid) {
						$.jGrowl('You cannot provide winner information and indicate there are no callers.', {
							header: 'Error',
							life: 10000,
							glue: 'before'
						});
						return;
					}
				} else { 
					valid = true;
					$("input[type='text']").each(function() {
						if ($.trim($(this).val()).length == 0) {
							valid = false;
						}
					});
					if ($("input[type='radio']:checked").length == 0) {
						valid = false;
					}
				}
				if ( ! valid) {
					$.jGrowl('You must either fill out the winner\'s name, phone number, email, address and delivery method or indicate that there were no callers.', {
						header: 'Error',
						life: 10000,
						glue: 'before'
					});
					return;
				}
				
				saveEvent('ScheduledTicketGiveawayEventInstance');
			});
			
			$("form input").change(function() {
				pendingEdits = true;
			});
			
			window.onbeforeunload = function() {
				if (pendingEdits) return "There may be unsaved changes that will be lost if you continue.";
			};
		});
	
		function saveEvent(type) {
			var attributes = {};
			var lastSelectedEvent;
			
			$('input', '#' + type + 'Form').each(function() {
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
			dbCommand('save', 'ScheduledTicketGiveawayInstance', 'MySql', attributes, {}, function(results) {
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
					pendingEdits = false;
					//trigger function to send emails
					$.ajax({
						url: 'ajax/sendgiveawayemails.php',
						method: 'GET',
						data: {
							scheduledEventId: '<?php echo $instance->ScheduledEventId; ?>',
							noCallers: $("[name=ScheduledTicketGiveawayEventInstanceNoCallers]").is(":checked") ? 1 : 0,
							winnerName: $("[name=ScheduledTicketGiveawayEventInstanceWinnerName]").val(),
							winnerPhone: $("[name=ScheduledTicketGiveawayEventInstanceWinnerPhone]").val(),
							winnerAddress: $("[name=ScheduledTicketGiveawayEventInstanceWinnerAddress]").val(),
							winnerEmail: $("[name=ScheduledTicketGiveawayEventInstanceWinnerEmail]").val(),
							isListenerMember: $("[name=ScheduledTicketGiveawayEventInstanceIsListenerMember]").is(":checked") ? 1 : 0,
							deliveryMethod: $("[name=ScheduledTicketGiveawayEventInstanceDeliveryMethod]:selected").val()
						}
					});
					window.close();
				}
				endLoading();
			});
		}
		
		function startLoading() {
			$('input').attr('disabled', true);
			$('select').attr('disabled', true);
			$('textarea').attr('disabled', true);
		}
		
		function endLoading() {
			$('input').removeAttr('disabled');
			$('select').removeAttr('disabled');
			$('textarea').removeAttr('disabled');
		}
	</script>

<?php ###################################################################### ?>
<?php $body=new BodyTemplateSection();                                     # ?>
<?php $body->setShowHeaderNav(false); $body->setShowSignOut(false); ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>

	<h4>Enter Giveaway Info</h4>

	<fieldset>
		<p>
			Please fill out this entire form, and press the save button.  This will automatically send all of the show information to the ticket winner's email.  If there is not a winner, check no callers.
		</p>
		
		<form id="ScheduledTicketGiveawayEventInstanceForm" action="giveaway.php">
		
			<div id="ScheduledTicketGiveawayEventInstance">
				<input type="hidden" name="ScheduledTicketGiveawayEventInstanceId" id="ScheduledTicketGiveawayEventInstanceId" value="<?php echo $instance->Id; ?>" >
				
				<div class="field">
					<label for="ScheduledTicketGiveawayEventInstanceNoCallers">No Callers</label>
					<input type="checkbox" name="ScheduledTicketGiveawayEventInstanceNoCallers" id="ScheduledTicketGiveawayEventInstanceNoCallers" <?php if ($instance->NoCallers) echo 'checked="checked"'; ?> <?php if ($disabled) { echo 'disabled="disabled"'; } ?>>
				</div>
				<div class="field">
					<label for="ScheduledTicketGiveawayEventInstanceWinnerName">Winner's Name</label>
					<input type="text" class="inputField" id="ScheduledTicketGiveawayEventInstanceWinnerName" name="ScheduledTicketGiveawayEventInstanceWinnerName" value="<?php echo $instance->WinnerName; ?>" <?php if ($disabled) { echo 'disabled="disabled"'; } ?>>
				</div>
				<div class="field">
					<label for="ScheduledTicketGiveawayEventInstanceWinnerPhone">Winner's Phone Number</label>
					<input type="text" class="inputField" id="ScheduledTicketGiveawayEventInstanceWinnerPhone" name="ScheduledTicketGiveawayEventInstanceWinnerPhone" value="<?php echo $instance->WinnerPhone; ?>" <?php if ($disabled) { echo 'disabled="disabled"'; } ?>>
				</div>
				<div class="field">
					<label for="ScheduledTicketGiveawayEventInstanceWinnerEmail">Winner's Email</label>
					<input type="text" class="inputField" id="ScheduledTicketGiveawayEventInstanceWinnerEmail" name="ScheduledTicketGiveawayEventInstanceWinnerEmail" value="<?php echo $instance->WinnerEmail; ?>" <?php if ($disabled) { echo 'disabled="disabled"'; } ?>>
				</div>
				<div class="field">
					<label for="ScheduledTicketGiveawayEventInstanceWinnerAddress">Winner's Address</label>
					<input type="text" class="inputField" id="ScheduledTicketGiveawayEventInstanceWinnerAddress" name="ScheduledTicketGiveawayEventInstanceWinnerAddress" value="<?php echo $instance->WinnerAddress; ?>" <?php if ($disabled) { echo 'disabled="disabled"'; } ?>>
				</div>
				<div class="field">
					<label for="ScheduledTicketGiveawayEventInstanceIsListenerMember">Mark if the winner is a KGNU listener-member</label>
					<input type="checkbox" name="ScheduledTicketGiveawayEventInstanceIsListenerMember" id="ScheduledTicketGiveawayEventInstanceIsListenerMember" <?php if ($instance->IsListenerMember) echo 'checked="checked"'; ?> <?php if ($disabled) { echo 'disabled="disabled"'; } ?>>
				</div>
				<div class="field">
					<label for="ScheduledTicketGiveawayEventInstanceIsListenerMember">Delivery method</label>
					<input type="radio" name="ScheduledTicketGiveawayEventInstanceDeliveryMethod" id="ScheduledTicketGiveawayEventInstanceDeliveryMethod" value="Pick Up in Studio" <?php if ($instance->DeliveryMethod == 'Pick Up in Studio') echo 'checked="checked"'; ?> <?php if ($disabled) { echo 'disabled="disabled"'; } ?>>
					Pick up In Studio
					&nbsp;
					<input type="radio" name="ScheduledTicketGiveawayEventInstanceDeliveryMethod" id="ScheduledTicketGiveawayEventInstanceDeliveryMethod" value="Mail Tickets" <?php if ($instance->DeliveryMethod == 'Mail Tickets') echo 'checked="checked"'; ?> <?php if ($disabled) { echo 'disabled="disabled"'; } ?>>
					Mail Tickets
				</div>
				<input type="submit" value="Save" id="saveButton" <?php if ($disabled) { echo 'disabled="disabled"'; } ?>>
				<input type="button" onclick="window.close();" value="Cancel" id="cancelButton">
			</div>
		
		</form>
		
	</fieldset>
	
<?php ###################################################################### ?>
<?php $close=new CloseTemplateSection();                                   # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
