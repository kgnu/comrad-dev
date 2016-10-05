<?php $this->Html->css('fullcalendar.css', null, array('inline' => false)); ?>
<?php $this->Html->script('fullcalendar.js', array('inline' => false)); ?>
<?php $this->Html->script('schedule/index.js', array('inline' => false)); ?>

<div class="row-fluid">
	<div class="span12">
		<div class="row-fluid">
			<div class="span4">
				<p id="title" class="lead pull-left" style="margin: 0"></p>
			</div>
			<div class="span5"></div>
			<div class="span3">
				<div class="btn-toolbar pull-right" style="margin: 0">
					<div class="btn-group">
						<a id="previous" class="btn" href=""><i class="icon-chevron-left"></i></a>
						<a id="next" class="btn" href=""><i class="icon-chevron-right"></i></a>
					</div>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div id="calendar"></div>
		</div>
	</div>
</div>
