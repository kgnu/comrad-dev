<?php require_once('initialize.php'); ?>

<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();                                   # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Music Library</title>

	<link type="text/css" rel="stylesheet" href="css/jquery/flexigrid/flexigrid.css" />
	<link type="text/css" rel="stylesheet" href="css/jquery/autocomplete/jquery.autocomplete.css" />
	
	<script type="text/javascript" src="js/jquery/ajaxqueue/jquery.ajaxqueue.js"></script>
	<script type="text/javascript" src="js/jquery/bgiframe/jquery.bgiframe.js"></script>
	<script type="text/javascript" src="js/jquery/dimensions/jquery.dimensions.js"></script>
	<script type="text/javascript" src="js/jquery/thickbox/thickbox.js"></script>
	<script type="text/javascript" src="js/jquery/autocomplete/jquery.autocomplete.js"></script>
	
	<script type='text/javascript' src='js/date/format/date.format.js'></script>
	
	<script type="text/javascript" src="js/jquery/flexigrid/flexigrid.js"></script>
	<script type="text/javascript" src="js/jquery/json/jquery.json.js"></script>
	<script type="text/javascript" src="js/jquery/ptags/jquery.ptags.js"></script>
	
	<script type="text/javascript" src="js/ajax/ajaxdbinterface.js"></script>
	<script type="text/javascript" src="js/ajax/itunessearch.js"></script>
	<script type="text/javascript" src="js/ajax/searchmusiccatalog.js"></script>
	
	<script type="text/javascript">
		$(function() {
			searchSubmit();
		});
	</script>
	
	<style type="text/css" media="screen">
		.dialog { display: none }
		
		table { width: 100%; border-collapse:collapse }
		tr { background-color: #fff }
		tr.even { background-color: #f1f7fb }
		tr.hover { background-color: #ebf1f5 }
		th { text-align: left }
		td { white-space: nowrap }
		
		form div.field { padding-top: 3px }
		form div.field label { display: inline-block; width: 80px; text-align: right; float: left; margin-top: 4px }
		form div.field div.input { margin-left: 80px; margin-right: 20px }
		form div.field input, form div.field select { text-align: left; margin-left: 6px; width: 100% }
		form div.field input[type="checkbox"] { width: auto }
		
		div.search_results { margin-top: 20px; background: #fff }
		td.albumArt, th.compilation, td.compilation, th.trackNumber, td.trackNumber, th.trackDiscNumber, td.trackDiscNumber { text-align: center }
		td.albumArt img { width: 25px; height: 25px; margin: 2px }
		
		#edit_album {  }
		#edit_album .album_information { width: 250px; float: left }
		#edit_album .track_information { margin-left: 260px }
		
		#edit_album_tracks { margin-top: 20px }
		
		#manage_album_tracks { width: 800px; margin: 100px auto }

	</style>
	
<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();                                   # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>

	<div id="search">
		<h4>Search Music Library</h4>
		<div id="search_params">
			<form onsubmit="searchSubmit(); return false;">
				<label for="search_input">Keyword Search:</label>
				<input id="search_input" type="text"></input>
				<button id="search_submit" type="submit">Search</button>
			</form>
		</div>
		<div id="search_albums">
			<div class="search_results"></div>
		</div>
		<button id="add_album_button" type="button" onclick="showAddAlbum();">Add New Album</button>
		<script type="text/javascript">
			function searchSubmit() {
				$('#search_albums div.search_results').html('');
				
				musicCatalogKeywordSearch({
					type: 'album',
					q: $('#search_input').val(),
					sortcolumn: 'AddDate',
					ascending: false,
					limit: 100
				}, function(results) {
					if (results.length > 0) {
						var table = $('<table />').append(
							$('<tr />').append(
								$('<th />')
							).append(
								$('<th>CD Code</th>')
							).append(
								$('<th>Title</th>')
							).append(
								$('<th class="compilation">Compilation</th>')
							).append(
								$('<th>Artist</th>')
							).append(
								$('<th>Genre</th>')
							).append(
								$('<th>Added</th>')
							).append(
								$('<th />')
							).append(
								$('<th />')
							)
						);
						$.each(results, function(i, album) {
							table.append(
								$('<tr class="search_result' + (i % 2 == 0 ? ' even' : '') + '" />').append(
									$('<td class="albumArt" />').append(
										$('<img src="' + album.Attributes.AlbumArt + '">')
									)
								).append(
									$('<td class="cdCode">' + album.Attributes.AlbumID + '</td>')
								).append(
									$('<td class="albumName">' + album.Attributes.Title + '</td>')
								).append(
									$('<td class="compilation">' + (album.Attributes.Compilation ? '<input type="checkbox" checked="checked" disabled="disabled" />' : '') + '</td>')
								).append(
									$('<td class="artistName">' + (album.Attributes.Artist ? album.Attributes.Artist : '') + '</td>')
								).append(
									$('<td class="genre">' + album.Attributes.Genre.Attributes.Name + '</td>')
								).append(
									$('<td class="addDate">' + new Date(album.Attributes.AddDate * 1000).format('m/d/yyyy') + '</td>')
								).append(
									$('<td class="edit" />').append(
										$('<button type="button">Edit</button>').unbind('click').click(function(eventObject) {
											showEditAlbum(album);
										})
									)
								).append(
									$('<td class="delete" />').append(
										$('<button type="button">Delete</button>').unbind('click').click(function(eventObject) {
											if (confirm('Are you sure?')) {
												dbCommand('delete', album.Type, 'MySql', album.Attributes, {}, function(response) {
													if (response.error) {
														$.each(response.error, function(key, value) {
															alert(key + ': ' + value);
														});
													} else {
														searchSubmit();
													}
												});
											}
										})
									)
								)
							);
						});
						
						$('#search_albums div.search_results').append(table);
					} else {
						$('#search_albums div.search_results').html('No results match the query.');
					}
				});
			}
		</script>
	</div>
	
	<div id="edit_album" class="dialog">
		<form onsubmit="editAlbumSubmit(); return false;">
			<div class="field"><label for="edit_album_Title">Album Title:</label><div class="input"><input type="text" id="edit_album_Title"></div></div>
			<div class="field hide_for_compilation"><label for="edit_album_Artist">Artist:</label><div class="input"><input type="text" id="edit_album_Artist"></div></div>
			<div class="field"><label for="edit_album_Label">Label:</label><div class="input"><input type="text" id="edit_album_Label"></div></div>
			<div class="field"><label for="edit_album_GenreID">Genre:</label><div class="input">
				<select id="edit_album_GenreID">
					<option value="">Select a Genre</option>
					<?php foreach (DB::getInstance('MySql')->find(new Genre(array('TopLevel' => true)), $count,  array('sortcolumn' => 'Name', 'limit' => false)) as $genre): ?>
					<option value="<?php echo $genre->GenreID ?>"><?php echo $genre->Name ?></option>
					<?php endforeach; ?>
				</select>
			</div></div>
			<div class="field"><label for="edit_album_Local">Local:</label><div class="input"><input type="checkbox" id="edit_album_Local"></div></div>
			<div class="field"><label for="edit_album_Compilation">Compilation:</label><div class="input"><input type="checkbox" id="edit_album_Compilation"></div></div>
			<div id="edit_album_tracks">
				<div class="tracks"></div>
				<button id="add_track_button" type="button">Add a Track</button>
			</div>
			<input id="edit_album_AlbumID" type="hidden"></input>
		</form>
		<script type="text/javascript">
			$(function() {
				$('#edit_album').dialog({
					autoOpen: false,
					modal: true,
					closeOnEscape: true,
					resizable: false,
					width: 625,
					position: 'top'
				});
				
				$('#edit_album_Compilation').change(function(eventObject) {
					if ($('#edit_album_Compilation').is(':checked')) {
						$('.hide_for_compilation').hide();
						$('.show_for_compilation').show();
					} else {
						$('.hide_for_compilation').show();
						$('.show_for_compilation').hide();
					}
				});
			});
			
			function showAddAlbum() {
				$('#edit_album').dialog('option', 'title', 'Add Album');
				
				$('#edit_album').dialog('option', 'buttons', {
					'Cancel': function() {
						$(this).dialog('close');
					},
					'Add Album': function() {
						addAlbumSubmit();
					}
				});
				
				$('#edit_album_tracks').hide();
				
				$('#edit_album_Title').val('');
				$('#edit_album_Artist').val('');
				$('#edit_album_Label').val('');
				$('#edit_album_GenreID').val('');
				$('#edit_album_Local').removeAttr('checked');
				$('#edit_album_Compilation').removeAttr('checked');
				
				$('#edit_album_Compilation').change();
				
				$('#edit_album').dialog('open');
				
				$('#edit_album_Title').focus();
			}
			
			function showEditAlbum(album) {
				$('#edit_album').dialog('option', 'title', 'Edit Album');
				
				$('#edit_album').dialog('option', 'buttons', {
					'Cancel': function() {
						$(this).dialog('close');
					},
					'Save Album': function() {
						editAlbumSubmit();
					}
				});
				
				reloadTracksForAlbum(album);
				
				$('#add_track_button').click(function(eventObject) {
					showAddTrack(album);
				});
				
				// Populate the fields
				$('#edit_album_AlbumID').val(album.Attributes.AlbumID);
				$('#edit_album_Title').val(album.Attributes.Title);
				$('#edit_album_Artist').val(album.Attributes.Artist ? album.Attributes.Artist : '');
				$('#edit_album_Label').val(album.Attributes.Label);
				$('#edit_album_GenreID').val(album.Attributes.GenreID);
				if (album.Attributes.Local) {
					$('#edit_album_Local').attr('checked', 'checked');
				} else {
					$('#edit_album_Local').removeAttr('checked');
				}
				if (album.Attributes.Compilation) {
					$('#edit_album_Compilation').attr('checked', 'checked');
				} else {
					$('#edit_album_Compilation').removeAttr('checked');
				}
				
				$('#edit_album_Compilation').change();
				
				$('#edit_album').dialog('open');
			}
			
			function addAlbumSubmit() {
				var album = {
					Type: 'Album',
					Attributes: {
						Title: $('#edit_album_Title').val(),
						Label: $('#edit_album_Label').val(),
						GenreID: $('#edit_album_GenreID').val(),
						Local: $('#edit_album_Local').is(':checked'),
						Compilation: $('#edit_album_Compilation').is(':checked')
					}
				}
				
				if (!album.Attributes.Compilation) album.Attributes.Artist = $('#edit_album_Artist').val();
				
				dbCommand('insert', album.Type, 'MySql', album.Attributes, {}, function(savedAlbum) {
					if (savedAlbum && !savedAlbum.error) {
						$('#edit_album').dialog('close');
						album.Attributes.AlbumID = savedAlbum.AlbumID;
						showEditAlbum(album);
						searchSubmit();
					}
				});
			}
			
			function editAlbumSubmit() {
				var album = {
					Type: 'Album',
					Attributes: {
						AlbumID: $('#edit_album_AlbumID').val(),
						Title: $('#edit_album_Title').val(),
						Label: $('#edit_album_Label').val(),
						GenreID: $('#edit_album_GenreID').val(),
						Local: $('#edit_album_Local').is(':checked'),
						Compilation: $('#edit_album_Compilation').is(':checked')
					}
				}
				
				if (!album.Attributes.Compilation) album.Attributes.Artist = $('#edit_album_Artist').val();
				
				dbCommand('save', album.Type, 'MySql', album.Attributes, {}, function(savedAlbum) {
					if (savedAlbum && !savedAlbum.error) {
						$('#edit_album').dialog('close');
						searchSubmit();
					}
				});
			}
			
			function reloadTracksForAlbum(album) {
				$('#edit_album_tracks').hide();
				
				$('#edit_album_tracks div.tracks').html('');
				
				findTracksInAlbum({
					albumid: album.Attributes.AlbumID
				}, function (tracks) {
					album.Attributes.Tracks = tracks;
					
					if (tracks.length > 0) {
						var table = $('<table />').append(
							$('<tr />').append(
								$('<th class="trackNumber">Track #</th>')
							).append(
								$('<th class="trackDiscNumber">Disc #</th>')
							).append(
								$('<th>Title</th>')
							).append(
								$('<th class="show_for_compilation">Artist</th>')
							).append(
								$('<th>Duration</th>')
							).append(
								$('<th />')
							).append(
								$('<th />')
							)
						);
						
						$.each(tracks, function(i, track) {
							table.append(
								$('<tr' + (i % 2 == 0 ? ' class="even"' : '') + ' />').append(
									$('<td class="trackNumber">' + track.Attributes.TrackNumber + '</td>')
								).append(
									$('<td class="trackDiscNumber">' + track.Attributes.DiskNumber + '</td>')
								).append(
									$('<td class="trackTitle">' + track.Attributes.Title + '</td>')
								).append(
									$('<td class="trackArtist show_for_compilation">' + track.Attributes.Artist + '</td>')
								).append(
									$('<td class="trackDuration">' + track.Attributes.Duration + '</td>')
								).append(
									$('<td class="edit" />').append(
										$('<button type="button">Edit</button>').unbind('click').click(function(eventObject) {
											showEditTrack(track, album);
										})
									)
								).append(
									$('<td class="delete" />').append(
										$('<button type="button" disabled="disabled">Delete</button>')
									)
								)
							);
						});
						
						$('#edit_album_tracks div.tracks').append(table);
						
						if ($('#edit_album_Compilation').is(':checked')) {
							$('.show_for_compilation').show();
						} else {
							$('.show_for_compilation').hide();
						}
					} else {
						$('#edit_album_tracks div.tracks').html('This Album has no Tracks.');
					}
				});
				
				$('#edit_album_tracks').show();
			}
		</script>
	</div>
	
	<div id="edit_track" class="dialog">
		<form onsubmit="editTrackSubmit(); return false;">
			<div class="field"><label for="edit_track_TrackNumber">Track #:</label><div class="input"><input type="text" id="edit_track_TrackNumber"></div></div>
			<div class="field"><label for="edit_track_DiskNumber">Disc #:</label><div class="input"><input type="text" id="edit_track_DiskNumber"></div></div>
			<div class="field"><label for="edit_track_Title">Track Title:</label><div class="input"><input type="text" id="edit_track_Title"></div></div>
			<div class="field show_for_compilation"><label for="edit_track_Artist">Artist:</label><div class="input"><input type="text" id="edit_track_Artist"></div></div>
			<div class="field"><label for="edit_track_Duration">Duration:</label><div class="input"><input type="text" id="edit_track_Duration"></div></div>
			<input id="edit_track_TrackID" type="hidden"></input>
			<input id="edit_track_AlbumID" type="hidden"></input>
		</form>
		<script type="text/javascript" charset="utf-8">
			$(function() {
				$('#edit_track').dialog({
					autoOpen: false,
					modal: true,
					closeOnEscape: true,
					resizable: false,
					width: 625,
					position: 'top'
				});
			});
			
			function showAddTrack(album) {
				$('#edit_track').dialog('option', 'title', 'Add Track');
				
				$('#edit_track').dialog('option', 'buttons', {
					'Cancel': function() {
						$(this).dialog('close');
					},
					'Add Track': function() {
						addTrackSubmit(album);
					}
				});
				
				$('#edit_track_TrackNumber').val('');
				$('#edit_track_DiskNumber').val('');
				$('#edit_track_Title').val('');
				$('#edit_track_Artist').val('');
				$('#edit_track_Duration').val('');
				$('#edit_track_AlbumID').val(album.Attributes.AlbumID);
				
				if (album.Attributes.Compilation) {
					$('.show_for_compilation').show();
				} else {
					$('.show_for_compilation').hide();
				}
				
				$('#edit_track').dialog('open');
				
				$('#edit_track_TrackNumber').focus();
			}
			
			function showEditTrack(track, album) {
				$('#edit_track').dialog('option', 'title', 'Edit Track');
				
				$('#edit_track').dialog('option', 'buttons', {
					'Cancel': function() {
						$(this).dialog('close');
					},
					'Save Track': function() {
						editTrackSubmit(album);
					}
				});
				
				// Populate the fields
				$('#edit_track_TrackID').val(track.Attributes.TrackID);
				$('#edit_track_TrackNumber').val(track.Attributes.TrackNumber);
				$('#edit_track_DiskNumber').val(track.Attributes.DiskNumber);
				$('#edit_track_Title').val(track.Attributes.Title);
				$('#edit_track_Artist').val(track.Attributes.Artist);
				$('#edit_track_Duration').val(track.Attributes.Duration);
				$('#edit_track_AlbumID').val(album.Attributes.AlbumID);
				
				if (album.Attributes.Compilation) {
					$('.show_for_compilation').show();
				} else {
					$('.show_for_compilation').hide();
				}
				
				$('#edit_track').dialog('open');
			}
			
			function addTrackSubmit(album) {
				var track = {
					Type: 'Track',
					Attributes: {
						TrackNumber: $('#edit_track_TrackNumber').val(),
						DiskNumber: $('#edit_track_DiskNumber').val(),
						Title: $('#edit_track_Title').val(),
						Duration: $('#edit_track_Duration').val(),
						AlbumID: $('#edit_track_AlbumID').val()
					}
				}
				
				if (album.Attributes.Compilation) track.Attributes.Artist = $('#edit_track_Artist').val();
				
				dbCommand('insert', track.Type, 'MySql', track.Attributes, {}, function(savedTrack) {
					if (savedTrack && !savedTrack.error) {
						$('#edit_track').dialog('close');
						reloadTracksForAlbum(album);
					}
				});
			}
			
			function editTrackSubmit(album) {
				var track = {
					Type: 'Track',
					Attributes: {
						TrackID: $('#edit_track_TrackID').val(),
						TrackNumber: $('#edit_track_TrackNumber').val(),
						DiskNumber: $('#edit_track_DiskNumber').val(),
						Title: $('#edit_track_Title').val(),
						Duration: $('#edit_track_Duration').val(),
						AlbumID: $('#edit_track_AlbumID').val()
					}
				}
				
				if (album.Attributes.Compilation) track.Attributes.Artist = $('#edit_track_Artist').val();
				
				dbCommand('save', track.Type, 'MySql', track.Attributes, {}, function(savedTrack) {
					if (savedTrack && !savedTrack.error) {
						$('#edit_track').dialog('close');
						reloadTracksForAlbum(album);
					}
				});
			}
		</script>
	</div>

<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();                                 # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
