<?php require_once('initialize.php'); ?>
<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();								   # ?>
<?php $head->write();													  # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Resources</title>

	<style type="text/css">
		.tabs { overflow:hidden; width:75%; box-sizing:border-box; margin:20px 0 0; }
		.tabs li { float:left; padding:4px 8px; border:1px solid #47777F; border-bottom:0; border-top-left-radius:3px; border-top-right-radius:3px; background:#007084; margin-top:4px; margin-right:4px; margin-bottom:0; cursor:pointer; list-style-type:none; color:#fff; }
		.tabs li.active { background:#74CCDC; color:#000; padding-top:8px; margin-top:0; }
		.tabContainer { width:75%; border:1px solid #007084; box-sizing:border-box; padding:5px; min-height:400px; }
		.tabContainer > div { display:none; }
		.tabContainer > div.active { display:block; }
	</style>
	
<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();								   # ?>
<?php $body->write();													  # ?>
<?php ###################################################################### ?>

	<ul class="tabs">
		<li class="active" name="announcements">Announcements</li>
		<li name="policies">Policies</li>
		<li name="pledgeDrive">Pledge Drive</li>
		<li name="other">Other Important Documents</li>
	</ul>
	
	
	<div class="tabContainer">

		<div name="announcements" class="active">

			<h4>Announcements</h4>
			
			<p><a href="#" target="_blank">Announcement Document 1</a></p>
			<p><a href="#" target="_blank">Announcement Document 2</a></p>
			<p><a href="#" target="_blank">Announcement Document 3</a></p>
		
		</div>
	
		<div name="policies">

			<h4>Policies</h4>
			
			<p><a href="resources/policies/KGNU Non-Commercial policy.pdf" target="_blank">Non-commercial Policy</a></p>
			<p><a href="resources/policies/KGNU Obscenity and Indecency Policy.pdf" target="_blank">Obscenity and Indecency Policy</a></p>
			<p><a href="resources/policies/KGNU Contribution Policy.pdf" target="_blank">Contribution Policy</a></p>
			<p><a href="resources/policies/Additional On-Air Policies.pdf" target="_blank">Additional On-Air Policies</a></p>
			<p><a href="resources/policies/Election Year Restrictions.pdf" target="_blank">Election Year Restrictions</a></p>
		
		</div>
		
		<div name="pledgeDrive">

			<h4>Pledge Drive</h4>
			
			<h5>How to Take a Pledge for KGNU</h5>
			<p style="margin-left:20px;">
				<iframe width="560" height="315" src="https://www.youtube.com/embed/pfO3KHITMtc" frameborder="0" allowfullscreen></iframe>
			</p>
			
		</div>
		
		<div name="other">

			<h4>Other Important Documents</h4>
			
			<p><a href="resources/other/KGNU 2014-2018 Strategic Plan.pdf" target="_blank">2014-2018 Strategic Plan</a></p>
			<p><a href="resources/other/KGNU Bylaws.pdf" target="_blank">Bylaws</a></p>
		
		</div>
		
	</div>
	
	<script type="text/javascript">
		$(function() {
			$(".tabs li").click(function() {
				var name = $(this).attr("name");
				$("[name]:not([name=" + name + "])").removeClass("active");
				$("[name=" + name + "]").addClass("active");
			});
		});
	</script>
	

<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();								 # ?>
<?php $close->write();													 # ?>
<?php ###################################################################### ?>
