<div class="row-fluid">
	<div class="span2">
		<?php echo $this->Html->image($track['Album']['a_AlbumArt'] ? $track['Album']['a_AlbumArt'] : 'album.png') ?>
		<p><span class="label label-info"><?php echo $track['Album']['Genre']['g_Name'] ?></span></p>
		<p>
			<?php if (isset($track['Album']['a_Label']) && $track['Album']['a_Label'] !== ''): ?>
				<span class="label"><?php echo $track['Album']['a_Label'] ?></span>
			<?php endif; ?>
		</p>
	</div>
	<div class="span10">
		<div class="btn-group pull-right">
			<?php echo $this->Html->link('Edit', array('action' => 'edit', $track['Track']['t_TrackID']), array('class' => 'btn')); ?>
			<?php echo $this->Html->link('Delete', array('action' => 'delete', $track['Track']['t_TrackID']), array('class' => 'btn btn-danger')); ?>
		</div>
		<h1>#<?php echo $track['Track']['t_TrackNumber'] ?>. <?php echo $track['Track']['t_Title'] ?></h1>
		<h2>
			<small>
				<i><?php echo $track['Album']['a_Compilation'] ? $track['Track']['t_Artist'] : $track['Album']['a_Artist'] ?></i>
				&mdash;
				<?php echo $this->Html->link($track['Album']['a_Title'], array('controller' => 'albums', 'action' => 'view', $track['Album']['a_AlbumID'])) ?>
			</small>
		</h2>
		<?php echo floor($track['Track']['t_Duration'] / 60).':'.($track['Track']['t_Duration'] % 60 < 10 ? '0' : '').($track['Track']['t_Duration'] % 60); ?>
		<?php if (count($track['FloatingShowElement']) > 0): ?>
		<h2>Played <?php echo count($track['FloatingShowElement']) ?> time<?php echo count($track['FloatingShowElement']) > 1 ? 's' : ''?>:</h2>
		<ul>
			<?php foreach ($track['FloatingShowElement'] as $trackPlay): ?>
				<li><?php echo $trackPlay['fse_Executed'] ?></li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
	</div>
</div>
