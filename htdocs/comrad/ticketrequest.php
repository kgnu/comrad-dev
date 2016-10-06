<?php

	require_once('initialize.php'); 
	
	if (isset($_POST['submit'])) {
		//send a ticket request email
		
		$emailBody = '<b>Ticket Request Information:</b><br />';
		$emailBody .= 'Volunteer Name: ' . $_POST['name'] . '<br />';
		$emailBody .= 'Phone: ' . $_POST['phone'] . '<br />';
		$emailBody .= 'Email: ' . $_POST['email'] . '<br />';
		$emailBody .= 'Artist/Venue/Date: ' . $_POST['artistVenueDate'] . '<br />';
		$emailBody .= 'Willing to Table Outreach: ' . $_POST['tableOutreach'] . '<br />';
		
		//make this an HTML email
		$headers  = 'MIME-Version: 1.0' . "\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
		
		// Additional headers
		$headers .= 'To: KGNU Tickets <tickets@kgnu.org>' . "\n";
		$headers .= 'From: KGNU Tickets <tickets@kgnu.org>' . "\n";

		mail('tickets@kgnu.org', 'Volunteer Ticket Request: ' . $_POST['artistVenueDate'], $emailBody, $headers);
	}
	
?>

<?php ###################################################################### ?>
<?php $head=new HeadTemplateSection();                                     # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Volunteer Ticket Request</title>

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

	<script type="text/javascript">
		var pendingEdits = false;
		
		$(function() {
			$("form").submit(function(e) {
				//validation
				var valid = true;
				$("input[type='text']").each(function() {
					if ($.trim($(this).val()).length == 0) {
						valid = false;
					}
				});
				if ($("input[type='radio']:checked").length == 0) {
					valid = false;
				}
				if ( ! valid) {
					$.jGrowl('Please fill out all fields.', {
						header: 'Error',
						life: 10000,
						glue: 'before'
					});
					e.preventDefault();
				} else {
					pendingEdits = false;
				}
				
				
			});
			
			$("form input").change(function() {
				pendingEdits = true;
			});
			
			window.onbeforeunload = function() {
				if (pendingEdits) return "There may be unsaved changes that will be lost if you continue.";
			};
		});
	
	</script>

<?php ###################################################################### ?>
<?php $body=new BodyTemplateSection();                                     # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>

	<h4>Submit Ticket Request</h4>

	
	<?php if (isset($_POST['submit'])): ?>
	
		<p> Thank you! Your request has been submitted. </p>
		
	<?php else: ?>
		
		<fieldset>
			<p>
				Please fill out the entire form to submit a ticket request.
			</p>
			
			<form id="TicketRequestForm" action="ticketrequest.php" method="post">
		
				<input type="hidden" name="ScheduledTicketGiveawayEventInstanceId" id="ScheduledTicketGiveawayEventInstanceId" value="<?php echo $instance->Id; ?>" >
				
				<div class="field">
					<label for="name">Volunteer Name</label>
					<input type="text" class="inputField" id="name" name="name" />
				</div>
				<div class="field">
					<label for="phone">Phone</label>
					<input type="text" class="inputField" id="phone" name="phone" />
				</div>
				<div class="field">
					<label for="email">Email</label>
					<input type="text" class="inputField" id="email" name="email" />
				</div>
				<div class="field">
					<label for="artistVenueDate">Artist/Venue/Date</label>
					<input type="text" class="inputField" id="artistVenueDate" name="artistVenueDate" />
				</div>
				<div class="field">
					<label for="tableOutreach">Willing to Table Outreach</label>
					<input type="radio" name="tableOutreach" id="tableOutreach" value="yes" />
					Yes
					&nbsp;
					<input type="radio" name="tableOutreach" id="tableOutreach" value="no" />
					No
				</div>
				<input type="submit" name="submit" value="Submit" id="saveButton">
			
			</form>
			
		</fieldset>
		
	<?php endif; ?>
	
<?php ###################################################################### ?>
<?php $close=new CloseTemplateSection();                                   # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
