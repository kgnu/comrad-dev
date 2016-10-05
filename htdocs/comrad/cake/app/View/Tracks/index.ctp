<ul>
	<?php foreach($tracks as $track): ?>
		<li><?php echo $this->Html->link($track['Track']['title'], array('controller' => 'tracks', 'action' => 'view', $track['Track']['id'])); ?></li>
	<?php endforeach; ?>
</ul>
<pre>
<?php print_r($tracks); ?>
</pre>
