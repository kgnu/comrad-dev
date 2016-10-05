<div class="row-fluid">
	<div class="span12">
		<div class="page-header">
			<h1>Edit Show</h1>
		</div>
		<?php echo $this->Form->create('ShowEvent', array('type' => 'put', 'class' => 'form-horizontal')); ?>
			<fieldset id="show">
				<?php echo $this->Form->input('e_Id', array('type' => 'hidden')); ?>
				<div class="row-fluid">
					<div class="span12">
						<?php echo $this->TB->input('e_Title', array(
							'label' => 'Title',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_HostId', array(
							'label' => 'Host',
							'empty' => true,
							'options' => $hosts
						)); ?>
						<?php echo $this->TB->input('e_HasHost', array(
							'label' => 'Has Host',
							'type' => 'checkbox',
							'checkbox_label' => 'Does the show have a host?'
						)); ?>
						<?php echo $this->TB->input('e_RecordAudio', array(
							'label' => 'Record Audio',
							'type' => 'checkbox',
							'checkbox_label' => 'Do we record audio for this show?'
						)); ?>
						<?php echo $this->TB->input('e_URL', array(
							'label' => 'Show URL',
							'type' => 'text'
						)); ?>
						<?php echo $this->TB->input('e_Source', array(
							'label' => 'Source',
							'options' => array(
								'KGNU' => 'KGNU',
								'Ext' => 'Ext'
							)
						)); ?>
						<?php echo $this->TB->input('e_Category', array(
							'label' => 'Category',
							'options' => array(
								'Announcements' => 'Announcements',
								'Mix' => 'Mix',
								'Music' => 'Music',
								'NewsPA' => 'NewsPA',
								'OurMusic' => 'OurMusic'
							)
						)); ?>
						<?php echo $this->TB->input('e_Class', array(
							'label' => 'Class',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_ShortDescription', array(
							'label' => 'Short Description',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_LongDescription', array(
							'label' => 'Long Description',
							'type' => 'textarea',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('e_Active', array(
							'label' => 'Active',
							'default' => true,
							'type' => 'checkbox',
							'checkbox_label' => 'Is this show active?'
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
