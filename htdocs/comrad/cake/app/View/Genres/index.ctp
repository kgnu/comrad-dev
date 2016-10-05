<ul>
	<?php foreach($genres as $genre): ?>
		<li><?php echo $this->Html->link($genre['Genre']['title'], array('controller' => 'genres', 'action' => 'view', $genre['Genre']['id'])); ?></li>
	<?php endforeach; ?>
</ul>
<pre>
<?php print_r($genres); ?>
</pre>
