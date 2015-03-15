<?php require_once('initialize.php'); ?>

<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();                                   # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Add Track</title>

	<link type="text/css" rel="stylesheet" href="css/jquery/autocomplete/jquery.autocomplete.css" />
	<link type="text/css" rel="stylesheet" href="css/jquery/jgrowl/jquery.jgrowl.css" />
	
	<script type="text/javascript" src="js/jquery/ajaxqueue/jquery.ajaxqueue.js"></script>
	<script type="text/javascript" src="js/jquery/bgiframe/jquery.bgiframe.js"></script>
	<script type="text/javascript" src="js/jquery/dimensions/jquery.dimensions.js"></script>
	<script type="text/javascript" src="js/jquery/thickbox/thickbox.js"></script>
	<script type="text/javascript" src="js/jquery/autocomplete/jquery.autocomplete.js"></script>
	
	<script type="text/javascript" src="js/jquery/flexigrid/flexigrid.js"></script>
	<script type="text/javascript" src="js/jquery/json/jquery.json.js"></script>
	<script type="text/javascript" src="js/jquery/ptags/jquery.ptags.js"></script>
	<script type="text/javascript" src="js/jquery/jgrowl/jquery.jgrowl.js"></script>
	<script type="text/javascript" src="js/jquery/jeditable/jquery.jeditable.js"></script>
	
	<script type="text/javascript" src="js/ajax/ajaxdbinterface.js"></script>
	<script type="text/javascript" src="js/ajax/itunessearch.js"></script>
	
	<style type="text/css" media="screen">
		table { border-collapse:collapse }
		table td { border: 1px solid #999; padding: 0px }
		table td div { padding: 0px 10px }
		table thead td { font-weight: bold; padding: 0px 10px; border-bottom: 2px solid #666; background-color: #ccf }
		table tbody td {  }
	</style>
	
	<script type="text/javascript">
	<!--
		
		$(function() {
			
		});
		
		function submit_add_track_form() {
			$('#results').html('Loading...');

			searchITunes({
				'term': $('#keywords').val(),
				'limit': 10,
				'media': 'music',
				'entity': 'musicTrack'
			}, function(response) {
				$('#results').html('');
				$.each(response.results, function(i, result) {
					$('#results').append(
						$('<div style="background: url(\'' + result.artworkUrl60 + '\') no-repeat left center; padding: 5px; padding-left: 68px; margin: 8px"></div>').append(
							$('<div style="font-size: 1.4em; font-weight: bold; ">' + result.trackName + '</div>')
						).append(
							$('<div style="font-size: 0.9em">by <span style="font-style: italic; color: #333">' + result.artistName + '</span></div>')
						).append(
							$('<div style="font-size: 0.9em; font-style: italic; color: #333">' + result.collectionName + '</div>')
						)
					);
				});
			});
		}
	-->
	</script>


<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();                                   # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>
	<h1>iTunes Search</h1>
	
	<div id="add_track">
		<form id="add_track_form" onsubmit="submit_add_track_form(); return false;">
			<label for="keywords">Search iTunes:</label>
			<input id="keywords" type="text">
			<input type="submit" value="Go">
			<div id="results"></div>
		</form>
	</div>

<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();                                 # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
