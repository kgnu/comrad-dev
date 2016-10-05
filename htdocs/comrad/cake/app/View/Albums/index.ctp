<ul>
	<?php foreach($albums as $album): ?>
		<li><?php echo $this->Html->link($album['Album']['a_Title'], array('controller' => 'albums', 'action' => 'view', $album['Album']['a_AlbumID'])); ?></li>
	<?php endforeach; ?>
</ul>
