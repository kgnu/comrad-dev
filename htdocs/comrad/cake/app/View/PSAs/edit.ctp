<div class="row-fluid">
	<div class="span12">
		<div class="page-header">
			<h1>Edit PSA</h1>
		</div>
		<?php echo $this->Form->create('PSAEvent', array('type' => 'put', 'class' => 'form-horizontal')); ?>
			<fieldset id="psa">
				<?php echo $this->Form->input('e_Id', array('type' => 'hidden')); ?>
				<div class="row-fluid">
					<div class="span12">
						<?php echo $this->TB->input('e_Title', array(
							'label' => 'Title',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_PSACategoryId', array(
							'label' => 'Category',
							'empty' => true,
							'options' => $psaCategories
						)); ?>
						<?php echo $this->TB->input('e_StartDate', array(
							'label' => 'Start Date',
							'type' => 'date',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_KillDate', array(
							'label' => 'Kill Date',
							'type' => 'date',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_OrgName', array(
							'label' => 'Organization Name',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_ContactName', array(
							'label' => 'Contact Name',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_ContactPhone', array(
							'label' => 'Contact Phone',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_ContactWebsite', array(
							'label' => 'Contact Website',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_ContactEmail', array(
							'label' => 'Contact Email',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_Copy', array(
							'label' => 'Copy',
							'type' => 'textarea',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_Active', array(
							'label' => 'Active',
							'default' => true,
							'type' => 'checkbox',
							'checkbox_label' => 'Is this PSA active?'
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
