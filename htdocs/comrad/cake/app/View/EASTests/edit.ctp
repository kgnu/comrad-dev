<div class="row-fluid">
	<div class="span12">
		<div class="page-header">
			<h1>Edit EAS Test</h1>
		</div>
		<?php echo $this->Form->create('EASTestEvent', array('type' => 'put', 'class' => 'form-horizontal')); ?>
			<fieldset id="eastest">
				<?php echo $this->Form->input('e_Id', array('type' => 'hidden')); ?>
				<div class="row-fluid">
					<div class="span12">
						<?php echo $this->TB->input('e_Title', array(
							'label' => 'Title',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_Active', array(
							'label' => 'Active',
							'default' => true,
							'type' => 'checkbox',
							'checkbox_label' => 'Is this EAS test active?'
						)); ?>
					</div>
				</div>
			</fieldset>
			<fieldset>
				<div class="form-actions">
					<?php echo $this->TB->button('Save', array('style' => 'primary')); ?>
					<?php echo $this->Html->link('Cancel', $this->request->referer(), array('class' => 'btn')); ?>
				</div>
			</fieldset>
		<?php echo $this->Form->end(); ?>
	</div>
</div>
