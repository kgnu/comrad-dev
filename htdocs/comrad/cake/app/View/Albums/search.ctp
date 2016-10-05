<?php
	$this->Html->addCrumb('Albums', '/albums');
	if (isset($this->data['keyword'])) {
		$this->Html->addCrumb('Search', '/albums/search');
		$this->Html->addCrumb('"' . $this->data['keyword'] . '"');
	} else {
		$this->Html->addCrumb('Search');
	}
?>

<h1>Local Album Search</h1>
<?php
	echo $this->Form->create(false, array('class' => 'search'));
	echo $this->Form->submit('Search');
	echo $this->Form->input('keyword', array('label' => false));
	echo $this->Form->end();
?>

<?php if (isset($results)): ?>
	<?php echo count($results) > 0 ? count($results) : 'No' ?> result<?php if (count($results) !== 1): ?>s<?php endif; ?>
	<?php if (count($results) > 0): ?>
		<div class="album-tiles">
			<?php foreach($results as $album): ?>
				<?php echo $this->element('album_tile', array(
					'albumArtUrl' => $album['Album']['a_AlbumArt'],
					'albumTitle' => $album['Album']['a_Title'],
					'albumAddDate' => $album['Album']['a_AddDate'],
					'albumId' => $album['Album']['a_AlbumID'],
					'artistTitle' => $album['Album']['a_Artist'],
					'numTracks' => count($album['Track'])
				)) ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
