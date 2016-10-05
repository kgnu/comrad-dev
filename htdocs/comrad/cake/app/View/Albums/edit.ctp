<?php $this->Html->script('albums/edit.js', array('inline' => false)); ?>

<div class="row-fluid">
	<div class="span12">
		<div class="page-header">
			<h1>Edit Album</h1>
		</div>
		<?php echo $this->Form->create('Album', array('type' => 'put', 'class' => 'form-horizontal')); ?>
			<fieldset>
				<?php echo $this->Form->input('a_AlbumID', array('type' => 'hidden')); ?>
				<div class="row-fluid">
					<div class="span6">
						<?php echo $this->TB->input('a_Title', array(
							'label' => 'Album Title',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('a_Compilation', array(
							'label' => 'Compilation',
							'checkbox_label' => 'Is this album a compilation of tracks by various artists?'
						)); ?>
						<?php echo $this->TB->input('a_Artist', array(
							'label' => 'Artist',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
					</div>
					<div class="span6">
						<?php echo $this->TB->input('a_GenreID', array(
							'label' => 'Genre',
							'empty' => true,
							'options' => $genres
						)); ?>
						<?php echo $this->TB->input('a_Label', array(
							'label' => 'Label',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('a_AlbumArt', array(
							'label' => 'Album Art URL',
							'class' => 'input-large',
							'placeholder' => 'http://'
						)); ?>
						<?php echo $this->TB->input('a_Location', array(
							'label' => 'Location',
							'options' => array(
								'Gnu Bin' => 'Gnu Bin',
								'Personal' => 'Personal',
								'Library' => 'Library',
								'Digital Library' => 'Digital Library'
							)
						)); ?>
					</div>
				</div>
				<?php echo $this->Form->hidden('a_ITunesId'); ?>
			</fieldset>
			<fieldset>
				<div class="form-actions">
					<?php echo $this->TB->button('Save Album', array('style' => 'primary')); ?>
					<?php echo $this->Html->link('Cancel', $this->request->referer(), array('class' => 'btn')); ?>
				</div>
			</fieldset>
		<?php echo $this->Form->end(); ?>
	</div>
</div>
