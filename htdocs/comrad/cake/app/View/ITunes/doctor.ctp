<table class="table">
	<?php foreach ($albums as $album): ?>
		<tr>
			<td>
				<p><?php echo $album['Album']['a_AlbumID'] ?></p>
				<p><?php echo $album['Album']['a_Title'] ?></p>
				<p><?php echo $album['keywords'] ?></p>
				<p><img src="<?php echo $album['Album']['a_AlbumArt'] ?>" /></p>
				<p><?php echo $album['Album']['a_AlbumArt'] ?></p>
				<?php echo $this->Html->link('View', array('controller' => 'albums', 'action' => 'view', $album['Album']['a_AlbumID']), array('class' => 'btn', 'target' => '_blank')) ?>
				<?php echo $this->Html->link('Edit', array('controller' => 'albums', 'action' => 'edit', $album['Album']['a_AlbumID']), array('class' => 'btn', 'target' => '_blank')) ?>
				<button class="btn" onclick="$('#album<?php echo $album['Album']['a_AlbumID'] ?>').toggle();">Show</button>
				<div id="album<?php echo $album['Album']['a_AlbumID'] ?>" style="display: none">
					<?php debug($album['Album']); ?>
				</div>
			</td>
			<td><?php echo (isset($album['success']) && $album['success'] ? '<span class="label label-success">success</span>' : '<span class="label label-important">fail</span>') ?></td>
			<td>
				<table>
					<?php if (isset($album['response'])): ?>
						<?php foreach($album['response']['results'] as $result): ?>
							<tr>
								<td>
									<p><?php echo $result['collectionName'] ?></p>
									<p><img src="<?php echo $result['artworkUrl60'] ?>" /></p>
									<p><?php echo $result['artworkUrl60'] ?></p>
									<button class="btn" onclick="$('#itAlbum<?php echo $album['Album']['a_AlbumID'] ?><?php echo $result['collectionId'] ?>').toggle();">Show</button>
									<div id="itAlbum<?php echo $album['Album']['a_AlbumID'] ?><?php echo $result['collectionId'] ?>" style="display: none">
										<?php debug($result); ?>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</table>
			</td>
		</tr>
	<?php endforeach; ?>
</table>