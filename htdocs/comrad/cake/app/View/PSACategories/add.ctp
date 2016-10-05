<div class="row-fluid">
	<div class="span12">
		<div class="page-header">
			<h1>Add PSA Category</h1>
		</div>
		<?php echo $this->Form->create('PSACategory', array('type' => 'post', 'class' => 'form-horizontal')); ?>
			<fieldset id="psacategory">
				<div class="row-fluid">
					<div class="span12">
						<?php echo $this->TB->input('Title', array(
							'label' => 'Title',
							'type' => 'text',
							'class' => 'input-large'
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
