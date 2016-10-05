<div class="row-fluid">
	<div class="span2">
		<div class="well sidebar-nav">
			<ul class="nav nav-list">
				<li class="nav-header">Event Types</li>
				<li><?php echo $this->Html->link('Alerts', array('controller' => 'alerts', 'action' => 'index')); ?></li>
				<li><?php echo $this->Html->link('Announcements', array('controller' => 'announcements', 'action' => 'index')); ?></li>
				<li><?php echo $this->Html->link('EAS Tests', array('controller' => 'e_a_s_tests', 'action' => 'index')); ?></li>
				<li><?php echo $this->Html->link('Features', array('controller' => 'features', 'action' => 'index')); ?></li>
				<li><?php echo $this->Html->link('Legal IDs', array('controller' => 'legal_i_ds', 'action' => 'index')); ?></li>
				<li><?php echo $this->Html->link('PSAs', array('controller' => 'p_s_as', 'action' => 'index')); ?></li>
				<li><?php echo $this->Html->link('Shows', array('controller' => 'shows', 'action' => 'index')); ?></li>
				<li><?php echo $this->Html->link('Underwritings', array('controller' => 'underwritings', 'action' => 'index')); ?></li>
			</ul>
		</div>
	</div>
	<div class="span10">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>Title</th>
					<th>Producer</th>
					<th>Guest</th>
					<th>Active</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($features as $feature): ?>
					<tr>
						<td><?php echo $this->Html->link($feature['FeatureEvent']['e_Title'], array('controller' => 'features', 'action' => 'view', $feature['FeatureEvent']['e_Id'])); ?></td>
						<td><?php echo $feature['FeatureEvent']['e_ProducerName'] ?></td>
						<td><?php echo $feature['FeatureEvent']['e_GuestName'] ?></td>
						<td><?php echo $feature['FeatureEvent']['e_Active'] ?></td>
						<td style="white-space: nowrap">
							<div class="btn-group pull-right">
								<?php echo $this->Html->link($this->TB->icon('pencil'), array('controller' => 'features', 'action' => 'edit', $feature['FeatureEvent']['e_Id']), array('class' => 'btn btn-mini', 'escape' => false)); ?>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $this->Html->link('<i class="icon-plus"></i> Add Feature', array('action' => 'add'), array('class' => 'btn', 'escape' => false)); ?>
	</div>
</div>
