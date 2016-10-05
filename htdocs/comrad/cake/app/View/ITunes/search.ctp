<div class="row-fluid">
	<div class="span12">
		<div class="page-header">
			<h1>Search iTunes</h1>
		</div>
		<?php echo $this->Form->create(false, array('type' => 'get')); ?>
			<fieldset>
				<div class="input-append">
					<?php echo $this->TB->basic_input('q', array(
						'type' => 'text',
						'label' => false,
						'class' => 'input-xlarge',
						'value' => isset($this->params->query['q']) ? $this->params->query['q'] : ''
					)); ?><?php echo $this->TB->button('Search', array('style' => 'primary')); ?>
				</div>
			</fieldset>
		<?php echo $this->Form->end(); ?>
	</div>
</div>
<?php if (isset($response)): ?>
	<div class="row-fluid">
		<div class="span12">
			<p class="lead"><?php echo count($response['results']) > 0 ? count($response['results']) : 'No' ?> result<?php if (count($response['results']) !== 1): ?>s<?php endif; ?> for <strong><?php echo $this->params->query['q']; ?></strong></p>
		</div>
		<?php if (count($response['results']) > 0): ?>
			<div class="row-fluid">
				<?php foreach ($response['results'] as $i => $album): ?>
					<div class="span4">
						<div class="row-fluid" style="margin-top: 10px">
							<div class="span3">
								<?php echo $this->Html->image($album['artworkUrl100']); ?>
							</div>
							<div class="span9">
								<div><?php echo $this->Html->link($album['collectionName'], array('controller' => 'i_tunes', 'action' => 'view', $album['collectionId'])) ?></div>
								<div><i><?php echo $album['artistName'] ?></i></div>
								<div>
									<?php if (isset($album['localId']) && isset($album['localTrackCount'])): ?>
										<?php echo $this->Html->link($this->TB->icon('download', 'white').' '.$album['localTrackCount'].' Track'.($album['localTrackCount'] !== 1 ? 's' : '').' Imported', array('controller' => 'albums', 'action' => 'view', $album['localId']), array('class' => 'btn btn-mini btn-info', 'escape' => false)); ?>
									<?php else: ?>
										<?php echo $this->Form->create(false, array('type' => 'put', 'url' => array('controller' => 'albums', 'action' => 'add'))); ?>
											<?php echo $this->Form->hidden('iTunesAlbumId', array('value' => $album['collectionId'])); ?>
											<?php echo $this->TB->button($this->TB->icon('download').' Import', array('size' => 'mini')); ?>
										<?php echo $this->Form->end(); ?>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
					<?php if (($i + 1) % 3 === 0): ?>
						</div><div class="row-fluid">
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<p style="margin-top: 20px">Can't find what you're looking for?</p>
			<?php echo $this->Html->link('Add Album Manually &raquo;', array('controller' => 'albums', 'action' => 'add'), array('class' => 'btn btn-success', 'escape' => false))?>
		</div>
	</div>
<?php endif; ?>
