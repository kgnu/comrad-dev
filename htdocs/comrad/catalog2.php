<?php require_once('initialize.php'); ?>

<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();                                   # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Albums</title>

	<link rel="stylesheet" href="http://dev.jquery.com/view/trunk/plugins/autocomplete/jquery.autocomplete.css" type="text/css" />
	<link rel="stylesheet" href="js/jGrowl-1.2.4/jquery.jgrowl.css" type="text/css" />
	
	<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/autocomplete/lib/jquery.bgiframe.min.js"></script>
	<script type="text/javascript" src="http://dev.jquery.com/view/trunk/plugins/autocomplete/lib/jquery.dimensions.js"></script>
	
	<script type="text/javascript" src="js/autocomplete/jquery.autocomplete.min.js"></script>
	<script type="text/javascript" src="js/flexigrid/custom-flexigrid.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jquery.json.js" charset="utf-8"></script>
	<script type="text/javascript" src="js/jquery.ptags.js"></script>
	<script type="text/javascript" src="js/jGrowl-1.2.4/jquery.jgrowl.js"></script>
	<script type="text/javascript" src="js/jeditable/jquery.jeditable.js"></script>
	<script type="text/javascript" src="js/ajax/ajaxdbinterface.js"></script>
	
	<style type="text/css" media="screen">
		div.albums { border: 1px solid blue; padding: 10px }
		div.album { margin: 4px; border: 1px solid #ccc; background-color: #eef }
		div.album.expanded div.track span { display: inline-block }
		span { display: inline-block; margin: 0px }
		span.title { font-weight: bold }
		
		div.tracks div.track_number { text-align: center }
		
		table { border-collapse:collapse }
		table td { border: 1px solid #999; padding: 0px }
		table td div { padding: 0px 10px }
		table thead td { font-weight: bold; padding: 0px 10px; border-bottom: 2px solid #666; background-color: #ccf }
		table tbody td {  }
	</style>
	
	<script type="text/javascript">
		var offset = 0;
		var pageSize = 20;
		var numResults = 100; // TODO: Make this dynamically change with query results
		var defaultAlbumFormHTML;
		var defaultTrackFormHTML
	
		$(function() {
			defaultAlbumFormHTML = $('#add_album').html();
		
			$('#add_album').hide();
		
			$('#filter').change(function() { // TODO: Make this fire on keystroke!
				fetchAlbums(function(albums) {
					render(albums);
				});
			});
		
			$('#previous_page').click(function() {
				if (offset - pageSize >= 0) {
					offset -= pageSize;
		
					fetchAlbums(function(albums) {
						render(albums);
					});
				}
			});
		
			$('#next_page').click(function() {
				if (offset + pageSize < numResults) {
					offset += pageSize;
		
					fetchAlbums(function(albums) {
						render(albums);
					});
				}
			});
		
			$('#btn_add_album').click(function() {
				$('#add_album').toggle(200);
			});
		
			fetchAlbums(function(albums) {
				render(albums);
			});
		});
	
		function fetchAlbums(callback) {
			dbCommand('find', 'Album', 'MySql', {
				'Title': $('#filter').val() // TODO: Make this filter on all attributes & tracks too!
			}, {
				'offset': offset,
				'limit': pageSize,
				'sortcolumn': 'Title',
				'fuzzytextsearch': true
			}, function(albums) {
				callback(albums);
			});
		}
	
		function fetchTracks(albumId, callback) {
			dbCommand('find', 'Track', 'MySql', {
				'AlbumID': albumId
			}, {
				'sortcolumn': 'TrackNumber'
			}, function(tracks) {
				callback(tracks);
			});
		}
	
		function checkPageBoundaries() {
			$('#next_page').attr('disabled', (offset + pageSize >= numResults));
			$('#previous_page').attr('disabled', (offset - pageSize < 0));
		}
	
		function render(albums) {
			checkPageBoundaries();
		
			$('#albums').html('');
		
			$.each(albums, function(i, album) {
				var albumDiv = $('<div class="album"></div>');
				var tracksDiv = $('<div class="tracks"></div>');
			
				$('#albums').append(
					albumDiv.append(
						$('<span id="Album-' + album.AlbumID + '-Title" class="editable">' + album.Title + '</span>')
					).append(
						$('<span id="Album-' + album.AlbumID + '-Artist" class="editable">' + album.Artist + '</span>')
					).append(
						$('<span id="Album-' + album.AlbumID + '-Label" class="editable">' + album.Label + '</span>')
					).append(
						tracksDiv
					).prepend(
						$('<button>+</button>').click(function() {
							albumDiv.toggleClass('expanded');
						
							if (albumDiv.hasClass('expanded')) {
								$(this).text('-');
								expand(album, albumDiv, tracksDiv);
							} else {
								$(this).text('+');
								collapse(album, albumDiv, tracksDiv);
							}
						})
					)
				);
			});
		
			$('#status').html('Showing ' + offset + '-' + (offset + pageSize) + ' of ' + numResults);
		
			$('.editable').editable(function(value, settings) {
				var split = this.id.split('-');
			
				var type = split[0];
				var id = split[1];
				var attribute = split[2];
		
				var attributes = {};
				attributes[type + 'ID'] = id;
				attributes[attribute] = value;
		
				dbCommand('update', type, 'MySql', attributes, {}, function(response) {
					if (response.error) {
						alert('Error updating ' + type + '...');
					}
				});
		
				return value;
			}, {
				tooltip: 'Double click to edit...',
	     	event: 'dblclick',
				submit: 'OK',
				cancel: 'Cancel',
				placeholder: 'Double click to edit...',
				width: '200px'
			});
		}
	
		function expand(album, albumDiv, tracksDiv) {
			tracksDiv.html('Loading Tracks...');
		
			fetchTracks(album.AlbumID, function(tracks) { // TODO: Cache tracks
				tracksDiv.html('');
			
				if (tracks.length == 0) {
					tracksDiv.html('No Tracks');
				} else {
					var table = $('<table><thead><tr><td>Track #</td><td>Disk #</td><td>Title</td><td>Artist</td><td>Duration</td></tr></thead></table>');
					var tableBody = $('<tbody></tbody>');
							
					$.each(tracks, function(i, track) {
						tableBody.append(
							$('<tr></tr>').append(
								$('<td><div id="Track-' + track.TrackID + '-TrackNumber' + '" class="editable">' + track.TrackNumber + '</div></td>')
							).append(
								$('<td><div id="Track-' + track.TrackID + '-DiskNumber' + '" class="editable">' + track.DiskNumber + '</div></td>')
							).append(
								$('<td><div id="Track-' + track.TrackID + '-Title' + '" class="editable">' + track.Title + '</div></td>')
							).append(
								$('<td><div id="Track-' + track.TrackID + '-Artist' + '" class="editable">' + track.Artist + '</div></td>')
							).append(
								$('<td><div id="Track-' + track.TrackID + '-Duration' + '" class="editable">' + track.Duration + '</div></td>')
							)
						);
					});
							
					tracksDiv.hide().append(table.append(tableBody)).show(200);
				}
			
				var addTrackDiv = $('<div id="' + album.AlbumID + '-add_track"></div>').append(
					$('<form></form>').submit(function() {
						return submitAddTrack(album.AlbumID, function(response) {
							expand(album, albumDiv, tracksDiv);
						});
					}).append(
						$('<p></p>').append(
							$('<label for="' + album.AlbumID + '-add_track_title">Title:</label>')
						).append(
							$('<input type="text" id="' + album.AlbumID + '-add_track_title">')
						)
					).append(
						$('<p></p>').append(
							$('<label for="' + album.AlbumID + '-add_track_tracknumber">Track #:</label>')
						).append(
							$('<input type="text" id="' + album.AlbumID + '-add_track_tracknumber">')
						)
					).append(
						$('<p></p>').append(
							$('<label for="' + album.AlbumID + '-add_track_disknumber">Disk #:</label>')
						).append(
							$('<input type="text" id="' + album.AlbumID + '-add_track_disknumber">')
						)
					).append(
						$('<p></p>').append(
							$('<label for="' + album.AlbumID + '-add_track_artist">Artist:</label>')
						).append(
							$('<input type="text" id="' + album.AlbumID + '-add_track_artist">')
						)
					).append(
						$('<p></p>').append(
							$('<label for="' + album.AlbumID + '-add_track_duration">Duration:</label>')
						).append(
							$('<input type="text" id="' + album.AlbumID + '-add_track_duration">')
						)
					).append(
						$('<p></p>').append(
							$('<input type="submit" value="Add">')
						)
					)
				);
		
				defaultTrackFormHTML = $('#' + album.AlbumID + '-add_track').html();
			
				tracksDiv.append(
					$('<button>Add Track</button>').click(function() {
						$('#' + album.AlbumID + '-add_track').toggle(200);
					})
				);

				tracksDiv.append(addTrackDiv.hide());
			
				$('.editable').editable(function(value, settings) {
					var split = this.id.split('-');
			
					var type = split[0];
					var id = split[1];
					var attribute = split[2];
		
					var attributes = {};
					attributes[type + 'ID'] = id;
					attributes[attribute] = value;
		
					dbCommand('update', type, 'MySql', attributes, {}, function(response) {
						if (response.error) {
							alert('Error updating ' + type + '...');
						}
					});
		
					return value;
				}, {
					tooltip: 'Double click to edit...',
		     	event: 'dblclick',
					submit: 'OK',
					cancel: 'Cancel',
					placeholder: 'Double click to edit...',
					width: '200px'
				});
			});
		}
	
		function collapse(album, albumDiv, tracksDiv) {
			tracksDiv.html('');
		}
	
		function submitAddAlbum(callback) {
			var attributes = {
				'Title': $('#add_album_title').val(),
				'Artist': $('#add_album_artist').val(),
				'Label': $('#add_album_label').val(),
				'GenreID': $('#add_album_genre').val(),
				'AddDate': 'NOW', // Translates "NOW" to time() when created in AbstractDBObject.php
				'Local': $('#add_album_local').attr('checked'),
				'Compilation': $('#add_album_compilation').attr('checked'),
				'CDCode': $('#add_album_cdcode').val(),
				'Location': $('#add_album_location').val()
			};
			
			dbCommand('insert', 'Album', 'MySql', attributes, {}, function(results) {
				if (results.error) {
					$.jGrowl('Check to make sure you have filled all the required fields and try again.  Required fields are marked by a red asterisk (*).', {
						header: 'Error',
						life: 10000,
						glue: 'before'
					});
				} else {
					$.jGrowl('The album has been successfully saved.', {
						header: 'Success',
						life: 10000,
						glue: 'before'
					});
					$('#add_album').hide(500, function() { $('#add_album').html(defaultAlbumFormHTML); });
				}
			});
	
			return false;
		}
	
		function submitAddTrack(albumId, callback) {
			var attributes = {
				'AlbumID': albumId,
				'TrackNumber': $('#' + albumId + '-add_track_tracknumber').val(),
				'DiskNumber': $('#' + albumId + '-add_track_disknumber').val(),
				'Title': $('#' + albumId + '-add_track_title').val(),
				'Artist': $('#' + albumId + '-add_track_artist').val(),
				'Duration': $('#' + albumId + '-add_track_duration').val()
			};
			
			dbCommand('insert', 'Track', 'MySql', attributes, {}, function(results) {
				if (results.error) {
					$.jGrowl('Check to make sure you have filled all the required fields and try again.  Required fields are marked by a red asterisk (*).', {
						header: 'Error',
						life: 10000,
						glue: 'before'
					});
				} else {
					$.jGrowl('The track has been successfully saved.', {
						header: 'Success',
						life: 10000,
						glue: 'before'
					});
					$('#add_album').hide(200, function() { $('#' + albumId + 'add_track').html(defaultTrackFormHTML); });
					callback(results);
				}
			
			});
		
			return false;
		}
	
	</script>


<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();                                   # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>
	<h1>Music Catalog</h1>
	
	<div id="control">
		<button id="btn_add_album">Add Album</button>
		<form onsubmit="fetchAlbums(function(albums) { render(albums); }); return false;">
			<label for="filter">Filter: </label>
			<input type="text" id="filter">
			<input type="submit" value="Go">
			<button onclick="$('#filter').val(''); fetchAlbums(function(albums) { render(albums); });">Clear</button>
		</form>
		<button id="previous_page">&lt;</button>
		<button id="next_page">&gt;</button>
		<span id="status"></span>
	</div>
	
	<div id="add_album">
		<form onsubmit="return submitAddAlbum();">
			<p>
				<label for="add_album_title">Title:</label>
				<input type="text" id="add_album_title">
			</p>
			
			<p>
				<label for="add_album_artist">Artist:</label>
				<input type="text" id="add_album_artist">
			</p>
			
			<p>
				<label for="add_album_label">Label:</label>
				<input type="text" id="add_album_label">
			</p>
			
			<p>
				<label for="add_album_genre">Genre:</label>
				<select id="add_album_genre">
					<?php
						$catalog = DB::getInstance('MySql');
						$genres = $catalog->find(new Genre(array('TopLevel' => true)));
						foreach($genres as $genre):
					?>
					<option value="<?php echo $genre->GenreID ?>"><?php echo $genre->Name ?></option>
					<?php endforeach; ?>
				</select>
			</p>
			
			<p>
				<label for="add_album_local">Local:</label>
				<input type="checkbox" id="add_album_local">
			</p>
			
			<p>
				<label for="add_album_compilation">Compilation:</label>
				<input type="checkbox" id="add_album_compilation">
			</p>
			
			<p>
				<label for="add_album_cdcode">CD Code:</label>
				<input type="text" id="add_album_cdcode">
			</p>
			
			<p>
				<label for="add_album_location">Location:</label>
				<select id="add_album_location">
					<option value="GNU Bin">GNU Bin</option>
					<option value="Personal">Personal</option>
					<option value="Library">Library</option>
					<option value="Digital Library">Digital Library</option>
				</select>
			</p>
			
			<p>
				<input type="submit" value="Add">
			</p>
		</form>
	</div>
	
	<div id="albums"></div>

<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();                                 # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
