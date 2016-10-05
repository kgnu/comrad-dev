<div class="row-fluid">
	<div class="span2">
		<div class="btn-group">
			<a class="btn dropdown-toggle input-block-level" data-toggle="dropdown" href="#">Add Music <span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><a href="#">From iTunes »</a></li>
				<li><a href="#">Manually »</a></li>
			</ul>
		</div>
		<div class="well sidebar-nav">
			<ul class="nav nav-list">
				<li class="active"><a href="#">All Music</a></li>
				<li><a href="#">Recently Added</a></li>
				<li><a href="#">Recently Played</a></li>
			</ul>
		</div>
	</div>
	<div class="span10">
		<div class="row-fluid">
			<div class="span12">
				<?php
					// echo $this->Form->create(false, array('type' => 'get', 'class' => 'form-search'));
					// echo $this->Form->input('q', array(
					// 	'type' => 'text',
					// 	'value' => $q,
					// 	'label' => false,
					// 	'placeholder' => 'Search albums...',
					// 	'div' => false,
					// 	'class' => 'search-query input-medium'
					// ));
					// echo $this->Form->button('Search', array('div' => false, 'class' => 'btn'));
					// echo $this->Form->end();
				?>
			</div>
		</div>
		<table class="table table-condensed">
			<thead>
				<tr>
					<th></th>
					<th>Album</th>
					<th>Artist</th>
					<th>Genre</th>
					<th>Added</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($albums as $album): ?>
					<tr>
						<td><?php echo $this->Html->image($album['Album']['a_AlbumArt'] ? $album['Album']['a_AlbumArt'] : 'album.png', array('class' => 'album-art')); ?></td>
						<td><?php echo $album['Album']['a_Title'] ?></td>
						<td><?php echo ($album['Album']['a_Compilation'] ? 'Various Artists' : $album['Album']['a_Artist']); ?></td>
						<td><?php echo ($album['Genre']['g_Name']); ?></td>
						<td><?php echo $this->Time->format('n/j/Y g:i a',$album['Album']['a_AddDate']); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
