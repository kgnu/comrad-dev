<div class="album-view">
	<aside class="operations">
		<?php if (isset($importedAlbum)): ?>
			<p>Imported <?php echo $this->Time->timeAgoInWords($importedAlbum['Album']['a_AddDate']) ?></p>
			<p><?php echo $this->Html->link('View local version', array('controller' => 'albums', 'action' => 'view', $importedAlbum['Album']['a_AlbumID'])) ?></p>
		<?php else: ?>
			<?php echo $this->Html->link('Import Album', array('action' => 'itunes_import', $album['id'])) ?>
		<?php endif; ?>
	</aside>
	<?php echo $this->Html->image($album['albumArt'] ? $album['albumArt'] : 'album.png', array('class' => 'album-art')) ?>
	<div class="details">
		<hgroup>
			<h1><?php echo $album['title'] ?></h1>
			<span>by <?php echo $album['artist'] ?></span>
		</hgroup>
		<section class="info">
			<p>Genre: <?php echo $album['genre'] ?></p>
			<p><?php echo $album['trackCount'] ?> track<?php if ($album['trackCount'] !== 1): ?>s<?php endif; ?></p>
		</section>
		<section class="tracks">
			<?php if (count($tracks) > 0): ?>
				<table>
					<tr>
						<th>Disc</th>
						<th>Track</th>
						<?php if ($album['compilation']): ?><th>Artist</th><?php endif; ?>
						<th>Name</th>
						<th>Duration</th>
						<th></th>
					</tr>
					<?php foreach($tracks as $track): ?>
						<tr class="track">
							<td><?php echo $track['discNumber'] ?></td>
							<td><?php echo $track['trackNumber'] ?></td>
							<?php if ($album['compilation']): ?><td><?php echo $track['artist'] ?></td><?php endif; ?>
							<td><?php echo $track['name'] ?></td>
							<td><?php echo $track['duration'] ?></td>
							<td>
								<?php echo $this->Html->link('preview', $track['previewUrl'])?>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
			<?php endif; ?>
		</section>
	</div>
</div>
