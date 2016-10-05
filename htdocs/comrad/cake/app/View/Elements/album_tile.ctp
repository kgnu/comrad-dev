<div class="album-tile">
	<?php echo $this->Html->image($albumArtUrl ? $albumArtUrl : 'album.png', array('class' => 'album-art')) ?>
	<div class="info">
		<span class="nowrap"><?php echo (isset($albumId) || isset($albumUrl)) ? $this->Html->link($albumTitle, isset($albumUrl) ? $albumUrl : array('controller' => 'albums', 'action' => 'view', $albumId)) : $albumTitle ?></span>
		<?php if (isset($artistTitle)): ?><span class="by nowrap">by <?php echo $artistTitle ?></span><?php endif; ?>
		<?php if (isset($numTracks)): ?><span class="num-tracks"><?php echo ($numTracks > 0 ? $numTracks : 'No') ?> track<?php if ($numTracks !== 1): ?>s<?php endif; ?></span><?php endif; ?>
		<?php if (isset($albumAddDate)): ?>
			<span class="added">Added <?php echo $this->Time->timeAgoInWords($albumAddDate) ?></span>
		<?php elseif (isset($iTunesId)): ?>
			<span class="import"><?php echo $this->Html->link('Import Tracks', array('controller' => 'albums', 'action' => 'itunes_import', $iTunesId)) ?></span>
		<?php endif; ?>
	</div>
</div>