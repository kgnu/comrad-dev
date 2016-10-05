<div class="row-fluid">
	<div class="span12">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>Title</th>
					<th>Starts</th>
					<th>Ends</th>
					<th>Repeat Type</th>
					<th>Repeat Interval</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($scheduledEvents as $scheduledEvent): ?>
					<tr>
						<td><?php echo $this->Html->link($scheduledEvent['Event']['e_Title'], array('controller' => 'scheduled_events', 'action' => 'view', $scheduledEvent['ScheduledEvent']['se_Id'])); ?></td>
						<td><?php echo $scheduledEvent['TimeInfo']['ti_StartDateTime'] ?></td>
						<td><?php echo $scheduledEvent['TimeInfo']['ti_EndDate'] ?></td>
						<td><?php echo $scheduledEvent['TimeInfo']['ti_DISCRIMINATOR'] ?></td>
						<td><?php echo $scheduledEvent['TimeInfo']['ti_Interval'] ?></td>
						<td style="white-space: nowrap">
							<div class="btn-group pull-right">
								<?php echo $this->Html->link($this->TB->icon('pencil'), array('controller' => 'scheduled_events', 'action' => 'edit', $scheduledEvent['ScheduledEvent']['se_Id']), array('class' => 'btn btn-mini', 'escape' => false)); ?>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $this->Html->link('<i class="icon-plus"></i> Add Scheduled Event', array('action' => 'add'), array('class' => 'btn', 'escape' => false)); ?>
	</div>
</div>
