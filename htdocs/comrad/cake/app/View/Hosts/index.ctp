<div class="row-fluid">
	<div class="span12">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th>Name</th>
					<th>Active</th>
					<th>Internal</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($hosts as $host): ?>
					<tr>
						<td><?php echo $this->Html->link($host['Host']['Name'], array('controller' => 'hosts', 'action' => 'view', $host['Host']['UID'])) ?></td>
						<td><?php echo $host['Host']['Active'] ?></td>
						<td><?php echo $host['Host']['Internal'] ?></td>
						<td style="white-space: nowrap">
							<div class="btn-group pull-right">
								<?php echo $this->Html->link($this->TB->icon('pencil'), array('controller' => 'hosts', 'action' => 'edit', $host['Host']['UID']), array('class' => 'btn btn-mini', 'escape' => false)); ?>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo $this->Html->link('<i class="icon-plus"></i> Add Host', array('action' => 'add'), array('class' => 'btn', 'escape' => false)); ?>
	</div>
</div>
