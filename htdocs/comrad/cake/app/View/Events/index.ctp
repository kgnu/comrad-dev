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
	</div>
</div>
