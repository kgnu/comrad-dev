<?php $this->Html->script('albums/add.js', array('inline' => false)); ?>

<div class="row-fluid">
	<div class="span12">
		<div class="page-header">
			<h1>Add Album</h1>
		</div>
		<?php echo $this->Form->create(false, array('type' => 'put', 'class' => 'form-horizontal')); ?>
			<?php if (isset($this->request->data['localAlbumId'])) echo $this->Form->hidden('localAlbumId'); ?>
			<?php if (isset($this->request->data['iTunesAlbumId'])) echo $this->Form->hidden('iTunesAlbumId'); ?>
			<fieldset id="album">
				<div class="row-fluid">
					<div class="span6">
						<?php echo $this->TB->input('Album.a_AlbumID', array(
							'label' => 'CD Code',
							'type' => 'text',
							'pattern' => '[0-9]*',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('Album.a_Title', array(
							'label' => 'Album Title',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('Album.a_Compilation', array(
							'label' => 'Compilation',
							'checkbox_label' => 'Is this album a compilation of tracks by various artists?'
						)); ?>
						<?php echo $this->TB->input('Album.a_Artist', array(
							'label' => 'Artist',
							'type' => 'text',
							'class' => 'input-large'
						)); ?>
					</div>
					<div class="span6">
						<?php echo $this->TB->input('Album.a_GenreID', array(
							'label' => 'Genre',
							'help_inline' => (isset($iTunesGenre) ? '<i style="cursor: pointer;" class="icon-info-sign" onclick="alert(\'iTunes lists the genre as: \n\n'.str_replace('\'', '\\\'', $iTunesGenre).'\');"></i>' : false),
							'empty' => true,
							'options' => $genres
						)); ?>
						<?php echo $this->TB->input('Album.a_Label', array(
							'label' => 'Label',
							'type' => 'text',
							'help_inline' => (isset($iTunesCopyright) ? '<i style="cursor: pointer;" class="icon-info-sign" onclick="alert(\'iTunes lists the copyright info as: \n\n'.str_replace('\'', '\\\'', $iTunesCopyright).'\');"></i>' : false),
							'class' => 'input-large'
						)); ?>
						<?php echo $this->TB->input('Album.a_Location', array(
							'label' => 'Location',
							'options' => array(
								'Gnu Bin' => 'Gnu Bin',
								'Personal' => 'Personal',
								'Library' => 'Library',
								'Digital Library' => 'Digital Library'
							)
						)); ?>
						<?php echo $this->TB->input('Album.a_AlbumArt', array(
							'label' => 'Album Art URL',
							'type' => 'url',
							'class' => 'input-large',
							'placeholder' => 'http://'
						)); ?>
					</div>
				</div>
				<?php echo $this->Form->hidden('Album.a_ITunesId'); ?>
			</fieldset>
			<fieldset id="tracks">
				<div class="control-group">
					<label for="Track0TDiskNumber" class="control-label">Tracks</label>
					<div class="controls">
						<table class="table table-condensed" style="width: inherit; margin-bottom: 6px;">
							<thead>
								<tr>
									<th>Disc</th>
									<th>#</th>
									<th>Track Name</th>
									<th class="track-artist">Artist</th>
									<th>Duration (s)</th>
									<?php if (!isset($this->request->data['iTunesAlbumId'])): ?><th></th><?php endif; ?>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($this->request->data['Track'] as $key => $track): ?>
									<tr>
										<td>
											<?php echo $this->Form->input('Track.'.$key.'.t_TrackID', array('type' => 'hidden')); ?>
											<?php echo $this->TB->input('Track.'.$key.'.t_DiskNumber', array(
												'type' => 'text',
												'pattern' => '[0-9]*',
												'label' => false,
												'class' => 'input-mini'
											)); ?>
										</td>
										<td>
											<?php echo $this->TB->input('Track.'.$key.'.t_TrackNumber', array(
												'type' => 'text',
												'pattern' => '[0-9]*',
												'label' => false,
												'class' => 'input-mini'
											)); ?>
										</td>
										<td>
											<?php echo $this->TB->input('Track.'.$key.'.t_Title', array(
												'type' => 'text',
												'label' => false,
												'class' => 'input-xlarge'
											)); ?>
										</td>
										<td class="track-artist">
											<?php echo $this->TB->input('Track.'.$key.'.t_Artist', array(
												'type' => 'text',
												'label' => false,
												'class' => 'input-xlarge'
											)); ?>
										</td>
										<td>
											<?php echo $this->TB->input('Track.'.$key.'.t_Duration', array(
												'type' => 'text',
												'pattern' => '[0-9]*',
												'label' => false,
												'class' => 'input-mini'
											)); ?>
										</td>
										<?php if (!isset($this->request->data['iTunesAlbumId'])): ?>
											<td>
												<a class="close" href="#" title="Remove track">&times;</a>
											</td>
										<?php endif; ?>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
						<?php if (!isset($this->request->data['iTunesAlbumId'])): ?>
							<div>
								<a href="#" class="btn btn-mini add-track" style="margin-left: 5px;"><i class="icon-plus-sign"></i> Add track</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
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
