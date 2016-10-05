<h1>iTunes Search</h1>
<?php
	echo $this->Form->create(false, array('class' => 'search'));
	echo $this->Form->input('term', array('label' => false));
	echo $this->Form->submit('Search');
	echo $this->Form->end();
?>

<?php if (isset($result)): ?>
	<?php echo count($result['results']) > 0 ? count($result['results']) : 'No' ?> result<?php if (count($result['results']) !== 1): ?>s<?php endif; ?>
	<?php if (count($result['results']) > 0): ?>
		<div class="album-tiles">
			<?php foreach ($result['results'] as $itAlbum): ?>
				<?php echo $this->element('album_tile', array(
					'albumArtUrl' => $itAlbum['artworkUrl60'],
					'albumTitle' => $itAlbum['collectionName'],
					'albumUrl' => array('controller' => 'albums', 'action' => 'itunes_view', $itAlbum['collectionId']),
					'artistTitle' => $itAlbum['artistName'],
					'numTracks' => $itAlbum['trackCount'],
					'iTunesId' => $itAlbum['collectionId']
				)) ?>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
<?php endif; ?>
