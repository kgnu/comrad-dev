<div class="row-fluid">
	<div class="span12">
		<div class="page-header">
			<h1>Add Feature</h1>
		</div>
		<?php echo $this->Form->create('FeatureEvent', array('type' => 'post', 'class' => 'form-horizontal')); ?>
			<fieldset id="feature">
				<div class="row-fluid">
					<div class="span12">
						<?php echo $this->TB->input('e_Title', array(
							'label' => 'Title',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_ProducerName', array(
							'label' => 'Producer',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_GuestName', array(
							'label' => 'Guest',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_Description', array(
							'label' => 'Description',
							'type' => 'textarea',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_InternalNote', array(
							'label' => 'Internal Note',
							'type' => 'textarea',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_Active', array(
							'label' => 'Active',
							'default' => true,
							'type' => 'checkbox',
							'checkbox_label' => 'Is this feature active?'
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
