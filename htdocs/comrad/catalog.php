<?php require_once('initialize.php'); ?>

<?php ###################################################################### ?>
<?php $head = new HeadTemplateSection();                                   # ?>
<?php $head->write();                                                      # ?>
<?php ###################################################################### ?>

	<title>comrad: <?php echo $init->getProp('Organization_Name'); ?> &bull; Albums</title>

	<link type="text/css" rel="stylesheet" href="css/jquery/flexigrid/flexigrid.css" />
	<link type="text/css" rel="stylesheet" href="css/jquery/autocomplete/jquery.autocomplete.css" />
	
	<script type="text/javascript" src="js/jquery/ajaxqueue/jquery.ajaxqueue.js"></script>
	<script type="text/javascript" src="js/jquery/bgiframe/jquery.bgiframe.js"></script>
	<script type="text/javascript" src="js/jquery/dimensions/jquery.dimensions.js"></script>
	<script type="text/javascript" src="js/jquery/thickbox/thickbox.js"></script>
	<script type="text/javascript" src="js/jquery/autocomplete/jquery.autocomplete.js"></script>
	
	<script type="text/javascript" src="js/jquery/flexigrid/flexigrid.js"></script>
	<script type="text/javascript" src="js/jquery/json/jquery.json.js"></script>
	<script type="text/javascript" src="js/jquery/ptags/jquery.ptags.js"></script>
	
	<script type="text/javascript">

	var selectedAlbumID = null;
	var selectedTrackID = null;
	var ajaxResultPair = null;
	var artistNames = [];  //to avoid displaying duplicate artist names
	
	var editingAlbum = false;    // Kind of hackish. Should maybe separate dialogs for adding/editing

	$(function() {

		// Create Albums flexigrid
		$("#albums").flexigrid({    
			url: 'ajax/getalbums.php',
			dataType: 'json',
			
			colModel: [
				{display: 'CD Code', name: 'CDCode', width: 40, align: 'left'},
				{display: 'Artist', name: 'Artist', width: 220, sortable: true, align: 'left'},
				{display: 'Title', name: 'Title', width: 220, sortable: true, align: 'left'},
				{display: 'Label', name: 'Label', width: 120, sortable: true, align: 'left'},
				{display: 'Genre', name: 'Genre', width: 60, align: 'left'},
				{display: 'Add Date', name: 'AddDate', width: 80, sortable: true, align: 'left'},
				{display: 'Local', name: 'Local', width: 34, align: 'center'},
				{display: 'Compilation', name: 'Compilation', width: 60, align: 'center'},
				{display: 'Location', name: 'Location', width: 70, align: 'left'},
			],
			    
			buttons: [
				{name: 'Add', bclass: 'add', onpress: addAlbum},
				{name: 'Edit', bclass: 'edit', onpress: editAlbum},
				{name: 'Delete', bclass: 'delete', onpress: deleteAlbum}
			],
			    
			searchitems: [
				{display: 'Artist', name: 'artist', isdefault: true},
				{display: 'Title', name: 'title'},
				{display: 'Label', name: 'label'}
			],
			    
			sortname: "Title",
			sortorder: "asc",
			usepager: true,
			title: 'Albums',
			useRp: true,
			rp: 25,       // rp = "rows per page"
			rpOptions: [10,25,50,75,100], 
			showTableToggleBtn: true,
			width: 'auto',
			height: 200,
			singleSelect: true,
			onSelect: selectAlbum,
			onDeselect: deselectAlbum
		});

		// Create Tracks flexigrid	
		$("#tracks").flexigrid({
			url: 'ajax/gettracks.php',
			dataType: 'json',
	
			colModel: [
				{display: 'Disk #', name: 'DiskNumber', width: 30, sortable: true, align: 'center'},
				{display: 'Track #', name: 'TrackNumber', width: 30, sortable: true, align: 'center'},
				{display: 'Title', name: 'Title', width: 400, sortable: true, align: 'left'},
				{display: 'Time', name: 'Duration', width: 40, sortable: true, align: 'left'},
				{display: 'Artist', name: 'Artist', width: 140, sortable: true, align: 'left', hide: true}
			],
				
			buttons: [
				{name: 'Add', bclass: 'add', onpress: addTrack},
				{name: 'Edit', bclass: 'edit', onpress: editTrack},
				{name: 'Delete', bclass: 'delete', onpress: deleteTrack},
				{separator: true}
			],
				
			searchitems: [
				{display: 'Artist', name: 'artist'},
				{display: 'Title', name: 'title', isdefault: true}
			],
	
			sortname: "TrackNumber",
			sortorder: "asc",
			usepager: true,
			title: 'Tracks',
			useRp: true,
			rp: 25,        // rp = "rows per page"
			rpOptions: [10,25,50,75,100], 
			showTableToggleBtn: true,
			width: 'auto',
			height: 200,
			singleSelect: true,
			onSelect: selectTrack,
			onDeselect: deselectTrack
		}).hide();
		
		$("#tracks").hide();

		function parseAJAXResults(data)
		{
			dataJSON = eval(data); 
			parsedData = []; 
			for (i = 0; i < dataJSON.length; i++)
			{ 
				obj = dataJSON[i]; 
				// Other internal autocomplete operations expect 
				// the data to be split into associative arrays of this sort 
				parsedData[i] = { 
					data: obj, 
					value: obj, 
					result: obj
				}; 
			} 

			artistNames = [];
			return parsedData ; 
		}

		// http://docs.jquery.com/Plugins/Autocomplete/autocomplete#url_or_dataoptions
		//Title Input
		$('#albumTitleInput').autocomplete('ajax/ajaxaws.php',
		 { 	 
		     minChars: 0,
		     cacheLength: 1,
		     formatItem: function(item)
		     {
				return item['album']['Title'];
		     },
		     extraParams:
			 {
		     	cmd: "ajaxAlbumName",
				JSON: function()
				{
					return $.toJSON({
						type: 'Album',
						attributes: {
							Title: $('#albumTitleInput').val(),
							Artist: $('#albumArtistInput').val(),
							Label: $('#albumLabelInput').val(),
						}
					});
				},
			 },
			 matchSubset: false,
			 parse: parseAJAXResults,
		 });
		$('#albumTitleInput').result(
				function(event, data, formatted)
				{
					ajaxResultPair = data;
					$('#albumTitleInput').val(data['album']['Title']);
					$('#albumArtistInput').val(data['album']['Artist']);
					$('#albumLabelInput').val(data['album']['Label']);	
				}
		)
		
		//Artist Input
		$('#albumArtistInput').autocomplete('ajax/ajaxaws.php',
		 { 	 
			 minChars: 0,
		     cacheLength: 1,
		     formatItem: function(item)
		     {
		     	if(artistNames.indexOf(item['album']['Artist']) == -1)
		     	{
			     	artistNames.push(item['album']['Artist']);
			     	return item['album']['Artist'];
		     	}
		     	else
		     	{
			     	return false;
		     	}
		     },
		     extraParams:
			 {
		     	cmd: "ajaxArtistName",
				JSON: function()
				{
					return $.toJSON({
						type: 'Album',
						attributes: {
							Title: $('#albumTitleInput').val(),
							Artist: $('#albumArtistInput').val(),
							Label: $('#albumLabelInput').val(),
						}
					});
				},
			 },
			 matchSubset: false,
			 parse: parseAJAXResults,
		 });
		$('#albumArtistInput').result(
				function(event, data, formatted)
				{
					$('#albumArtistInput').val(data['album']['Artist']);
					$('#albumLabelInput').val(data['album']['Label']);	
				}
		)
		
		//Label Input
		$('#albumLabelInput').autocomplete('ajax/ajaxaws.php',
		 { 	 
			 minChars: 0,
		     cacheLength: 1,
		     formatItem: function(item)
		     {
				return item['album']['Label'];
		     },
		     extraParams:
			 {
		     	cmd: "ajaxLabelName",
				JSON: function()
				{
					return $.toJSON({
						type: 'Album',
						attributes: {
							Title: $('#albumTitleInput').val(),
							Artist: $('#albumArtistInput').val(),
							Label: $('#albumLabelInput').val(),
						}
					});
				},
			 },
			 matchSubset: false,
			 parse: parseAJAXResults,
		 });
		$('#albumLabelInput').result(
				function(event, data, formatted)
				{
					$('#albumLabelInput').val(data['album']['Label']);	
				}
		)
		
		//Track Title Input
		$('#trackTitleInput').autocomplete('ajax/ajaxaws.php',
		 { 	 
			 minChars: 0,
		     cacheLength: 1,
		     max: 50,
		     formatItem: function(item)
		     {
				return item['Title'];
		     },
		     extraParams:
			 {
		     	cmd: "ajaxTrackName",
				JSON: function()
				{
					return $.toJSON({
						type: 'Track',
						attributes: {
							AlbumID: selectedAlbumID,
							Title: $('#trackTitleInput').val(),
						}
					});
				},
			 },
			 matchSubset: false,
			 parse: parseAJAXResults,
		 });
		$('#trackTitleInput').result(
				function(event, data, formatted)
				{
					$('#trackTitleInput').val(data['Title']);
					$('#trackTrackNumberInput').val(data['TrackNumber']);
					$('#trackDiskNumberInput').val(data['DiskNumber']);
					$('#trackArtistInput').val(data['Artist']);
					return;
				}
		)
		
		// GenreTags
		$('#tagsInput').autocomplete('ajax/ajaxdbinterface.php', {

			minChars: 1,
			cacheLength: 10,
			max: 51,
		    formatItem: function(item)
		    {
				return item['Name'];
		    },
			extraParams: {
				method: 'findSubGenres',
				params: $.toJSON({
					type: 'Genre',
					attributes: {
					}
				})
			},
			mustMatch: false,
			matchSubset: false,
			parse: parseAJAXResults

		}).result(function (event, item) {

			if (!item['create'])
			{
				$("#hiddenTagsInput").ptags_add(item['Name']);
			}
			else
			{
				// Add the new genre
				$.ajax({
					type: 'POST',
					url: 'ajax/ajaxdbinterface.php',
					dataType: 'json',
					data: {
						method: 'insert',
						params: $.toJSON({
							type: 'Genre',
							attributes: {
								Name: item['create'],
								TopLevel: false
							}
						}),
						db: 'MySql'
					},
					success: function(response)
					{
						$("#hiddenTagsInput").ptags_add(item['create']);
					}
				});
			}

			$("#tagsInput").val('');
		});

		// Add tag editor
	    $(document).ready(function() {
	    	$('#hiddenTagsInput').ptags();
	    });

		function dump(arr,level) {
			var dumped_text = "";
			if(!level) level = 0;
			
			//The padding given at the beginning of the line.
			var level_padding = "";
			for(var j=0;j<level+1;j++) level_padding += "    ";
			
			if(typeof(arr) == 'object') { //Array/Hashes/Objects 
				for(var item in arr) {
					var value = arr[item];
					
					if(typeof(value) == 'object') { //If it is an array,
						dumped_text += level_padding + "'" + item + "' ...\n";
						dumped_text += dump(value,level+1);
					} else {
						dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
					}
				}
			} else { //Strings/Chars/Numbers etc.
				dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
			}
			return dumped_text;
		}
		
		// Select, Deselect ALBUM
		function selectAlbum(selection)
		{
			selectedAlbumID = parseInt($('#albums .trSelected').attr('id').substring(3));
			
			// Send the AlbumID to the Track flexigrid
			$('#tracks').show().flexOptions({
				'qtype': 'AlbumID',
				'query': selectedAlbumID
			}).flexReload();
		}
		
		function deselectAlbum(deselection)
		{
			selectedAlbumID = null;
			$('#tracks').hide();
		}

		// Select, Deselect TRACK
		function selectTrack(selection)
		{
			selectedTrackID = parseInt($('#tracks .trSelected').attr('id').substring(3));
		}
		
		function deselectTrack(deselection)
		{
			selectedTrackID = null;
		}
		
		// Add, Delete, Edit ALBUM
		function addAlbum()
		{
			// Enable autocomplete (not very elegant, but JQuery doesn't have a disable option)
			$('#albumTitleInput').setOptions({ minChars: 0 });
			$('#albumLabelInput').setOptions({ minChars: 0 });
			$('#albumArtistInput').setOptions({ minChars: 0 });
			
			$('#albumIDInput').val('');
			$('#dialogAddAlbum').dialog('option', 'title', 'Add Album');
			$('#dialogAddAlbum').dialog('open');
		}
		
		function deleteAlbum()
		{
			if (selectedAlbumID)
			{
				$('#dialogDeleteAlbum').dialog('open');
			}
			else
			{
				alert('No selected album!');
			}
		}
			
		function editAlbum()
		{
			if (selectedAlbumID)
			{
				editingAlbum = true;

				// Disable autocomplete (not very elegant, but JQuery doesn't have a disable option)
				$('#albumTitleInput').setOptions({ minChars: 9999 });
				$('#albumLabelInput').setOptions({ minChars: 9999 });
				$('#albumArtistInput').setOptions({ minChars: 9999 });
				
				// Populate form fields
				$('#albumCDCodeInput').val($('#albums .trSelected td:nth-child(1) div').text());
				$('#albumArtistInput').val($('#albums .trSelected td:nth-child(2) div').text());
				$('#albumTitleInput').val($('#albums .trSelected td:nth-child(3) div').text());
				$('#albumLabelInput').val($('#albums .trSelected td:nth-child(4) div').text());
				$('#albumIDInput').val(selectedAlbumID);

				// Genre drop-down
				var genre = $('#albums .trSelected td:nth-child(5) div').text();
				$('#albumGenreInput').val($("#albumGenreInput option[text='" + genre + "']:first").val());

				// Local checkbox
				if ($('#albums .trSelected td:nth-child(7) div').text())
					$('#albumLocalInput').removeAttr('checked');
				else
					$('#albumLocalInput').attr('checked', true);

				// Compilation checkbox
				if ($('#albums .trSelected td:nth-child(8) div').text())
					$('#albumCompilationInput').removeAttr('checked');
				else
					$('#albumCompilationInput').attr('checked', true);

				// Location drop-down
				var genre = $('#albums .trSelected td:nth-child(9) div').text();
				$('#albumLocationInput').val($("#albumLocationInput option[text='" + genre + "']:first").val());

				// Open the dialog
				$('#dialogAddAlbum').dialog('option', 'title', 'Edit Album');
				$('#dialogAddAlbum').dialog('open');

				// Sub-Genre tags
				populateGenreTags();
			}
			else
			{
				alert('No selected album!');
			}
		}
		
		// Add, Delete, Edit TRACK
		function addTrack()
		{
			editingAlbum = false;

			// Enable autocomplete (not very elegant, but JQuery doesn't have a disable option)
			$('#trackTitleInput').setOptions({ minChars: 0 });
			
			$('#trackIDInput').val('');
			$('#trackTimeInput').val('0:00');
			$('#dialogAddTrack').dialog('option', 'title', 'Add Track');
			$('#dialogAddTrack').dialog('open');
		}
			
		function deleteTrack()
		{
			if (selectedTrackID)
			{
				$('#dialogDeleteTrack').dialog('open');
			}
			else
			{
				alert('No selected track!');
			}
		}
			
		function editTrack()
		{
			if (selectedTrackID)
			{
				// Disable autocomplete (not very elegant, but JQuery doesn't have a disable option)
				$('#trackTitleInput').setOptions({ minChars: 9999 });
				
				// Populate form fields
				$('#trackDiskNumberInput').val($('#tracks .trSelected td:nth-child(1) div').text());
				$('#trackTrackNumberInput').val($('#tracks .trSelected td:nth-child(2) div').text());
				$('#trackTitleInput').val($('#tracks .trSelected td:nth-child(3) div').text());
				$('#trackTimeInput').val($('#tracks .trSelected td:nth-child(4) div').text());
				$('#trackArtistInput').val($('#tracks .trSelected td:nth-child(5) div').text());
				$('#trackIDInput').val(selectedTrackID);

				// Open the dialog
				$('#dialogAddTrack').dialog('option', 'title', 'Edit Track');
				$('#dialogAddTrack').dialog('open');
			}
			else
			{
				alert('No selected track!');
			}
		}
	});


	function compareFieldsToPair(pair)
	{
		if(pair['album']['Title'] != $('#albumTitleInput').val() ||
		   pair['album']['Artist'] != $('#albumArtistInput').val() ||
		   pair['album']['Label'] != $('#albumLabelInput').val())
		{
			return false;
		}
		else
		{
			return true;
		}
	}	
	
	function submitAddAlbum()
	{
		disableAlbumForm();

		// Validate
		if(!editingAlbum && ajaxResultPair != null && compareFieldsToPair(ajaxResultPair))
		{
			// Ajax request
			$.ajax({
				type: 'POST',
				url: 'ajax/ajaxdbinterface.php',
				dataType: 'json',
				data: {
					method: 'insert',
					params: $.toJSON({
						type: 'Album',
						attributes: {
							Title: ajaxResultPair['album']['Title'],
							Artist: ajaxResultPair['album']['Artist'],
							Label: ajaxResultPair['album']['Label'],
							GenreID: $('#albumGenreInput option:selected').attr("value"),
							AddDate: "NOW", // Translates "NOW" to time() when created in AbstractDBObject.php
							Local: $('#albumLocalInput').is(':checked'),
							Compilation: $('#albumCompilationInput').is(':checked'),
							CDCode: $('#albumCDCodeInput').val() == 0 ? null : $('#albumCDCodeInput').val(),
							Location: $('#albumLocationInput').val()
						}
					}),
					db: 'MySql'
				},
				success: function(response)
				{
					enableAlbumForm();
					if (response.error != null)
						alert("Error: " + response.error);
					else
					{
						selectedAlbumID = response.AlbumID;
						submitEditTags();
						for(var track in ajaxResultPair['tracks'])
				     	{
							submitAWSTrack(ajaxResultPair['tracks'][track]);
				     	}
				     	ajaxResultPair = null;
						$('#dialogAddAlbum').dialog('close');
						$('#albums').flexReload();
					}
				}
			})
		}
		else if ($('#albumTitleInput').val() != '' &&
				$('#albumLabelInput').val() != '' &&
				$('#albumGenreInput').val() != '')
		{
			// Ajax request
			$.ajax({
				type: 'POST',
				url: 'ajax/ajaxdbinterface.php',
				dataType: 'json',
				data: {
					method: 'insert',
					params: $.toJSON({
						type: 'Album',
						attributes: {
							AlbumID: $('#albumIDInput').val(),
							Title: $('#albumTitleInput').val(),
							Artist: $('#albumArtistInput').val(),
							Label: $('#albumLabelInput').val(),
							GenreID: $('#albumGenreInput option:selected').attr("value"),
							AddDate: "NOW", // Translates "NOW" to time() when created in AbstractDBObject.php
							Local: $('#albumLocalInput').is(':checked'),
							Compilation: $('#albumCompilationInput').is(':checked'),
							CDCode: $('#albumCDCodeInput').val() == 0 ? null : $('#albumCDCodeInput').val(),
							Location: $('#albumLocationInput').val()
						}
					}),
					db: 'MySql'
				},
				success: function(response)
				{
					selectedAlbumID = response.AlbumID;
					submitEditTags();
					enableAlbumForm();
					if (response.error != null)
						alert("Error: " + response.error);
					else
					{
						$('#dialogAddAlbum').dialog('close');
						$('#albums').flexReload();
					}
				}
			})
		}
		else
		{
			alert('Invalid input');
			enableAlbumForm();
		}
	}

	function submitAWSTrack(track)
	{
		// Ajax request
		$.ajax({
			type: 'POST',
			url: 'ajax/ajaxdbinterface.php',
			dataType: 'json',
			data: {
				method: 'insert',
				params: $.toJSON({
					type: 'Track',
					attributes: {
						AlbumID: selectedAlbumID,
						Title: track['Title'],
						TrackNumber: track['TrackNumber'],
						DiskNumber: track['DiskNumber'],
						Duration: 0
					}
				}),
				db: 'MySql'
			},
			success: function(response)
			{
				enableTrackForm();
				if (response.error != null)
					alert("Error: " + response.error);
				else
				{
					$('#dialogAddTrack').dialog('close');
					$('#tracks').flexReload();
				}
			}
		})
	}

	function submitAddTrack()
	{
		if (!selectedAlbumID)
		{
			alert('An album must be selected');
			return;
		}
		
		disableTrackForm();
		
		// Validate
		if ($('#trackTitleInput').val() != '' &&
				$('#trackTrackNumberInput').val() != '' &&
				$('#trackDiskNumberInput').val() != '' &&
				$('#trackTimeInput').val() != '')
		{
			// Ajax request
			$.ajax({
				type: 'POST',
				url: 'ajax/ajaxdbinterface.php',
				dataType: 'json',
				data: {
					method: 'insert',
					params: $.toJSON({
						type: 'Track',
						attributes: {
							TrackID: $('#trackIDInput').val(),
							AlbumID: selectedAlbumID,
							Title: $('#trackTitleInput').val(),
							TrackNumber: $('#trackTrackNumberInput').val(),
							DiskNumber: $('#trackDiskNumberInput').val(),
							Artist: $('#trackArtistInput').val(),
							Duration: parseTime($('#trackTimeInput').val())
						}
					}),
					db: 'MySql'
				},
				success: function(response)
				{
					enableTrackForm();
					if (response.error != null)
						alert("Error: " + response.error);
					else
					{
						$('#dialogAddTrack').dialog('close');
						$('#tracks').flexReload();
					}
				}
			})
		}
		else
		{
			alert('Invalid input');
			enableTrackForm();
		}
	}

	function submitDeleteAlbum()
	{
		if (!selectedAlbumID)
		{
			alert('An album must be selected');
			return;
		}

		// Ajax request
		$.ajax({
			type: 'POST',
			url: 'ajax/ajaxdbinterface.php',
			dataType: 'json',
			data: {
				method: 'delete',
				params: $.toJSON({
					type: 'Album',
					attributes: {
						AlbumID: selectedAlbumID,
					}
				}),
				db: 'MySql'
			},
			success: function(response)
			{
				if (response.error != null)
					alert("Error: " + response.error);
				else
				{
					$('#dialogDeleteAlbum').dialog('close');
					$('#albums').flexReload();
				}
			}
		});
	}

	function submitDeleteTrack()
	{
		if (!selectedTrackID)
		{
			alert('A track must be selected');
			return;
		}

		// Ajax request
		$.ajax({
			type: 'POST',
			url: 'ajax/ajaxdbinterface.php',
			dataType: 'json',
			data: {
				method: 'delete',
				params: $.toJSON({
					type: 'Track',
					attributes: {
						TrackID: selectedTrackID,
					}
				}),
				db: 'MySql'
			},
			success: function(response)
			{
				if (response.error != null)
					alert("Error: " + response.error);
				else
				{
					$('#dialogDeleteTrack').dialog('close');
					$('#tracks').flexReload();
				}
			}
		});
	}

	function submitEditTags()
	{
		// Ajax request
		$.ajax({
			type: 'POST',
			url: 'ajax/ajaxdbinterface.php',
			dataType: 'json',
			data: {
				method: 'updateGenreTags',
				params: $.toJSON({
					type: 'Album',
					attributes: {
						AlbumID: selectedAlbumID
					},
					names: $("#hiddenTagsInput").val().split(',')
				})
			},
			success: function(response)
			{
				if (response.error != null)
					alert("Error: " + response.error);
			}
		});
	}

	function populateGenreTags()
	{
		// Ajax request
		$.ajax({
			type: 'POST',
			url: 'ajax/ajaxdbinterface.php',
			dataType: 'json',
			data: {
				method: 'getExistingTags',
				params: $.toJSON({
					type: 'GenreTag',
					attributes: {
						AlbumID: selectedAlbumID
					}
				}),
				db: 'MySql'
			},
			success: function(data, textStatus, XMLHttpRequest)
			{
				for (var i in data)
				{
					$("#hiddenTagsInput").ptags_add(data[i]);
				}
			}
		});
	}

	function parseTime(time)
	{
		var t = time.split(':');
		if (t.length == 1)
			return parseInt(t[0], 10);
		else if (t.length == 2)
			return parseInt(t[0], 10) * 60 + parseInt(t[1], 10);
		else
			return null;
	}
	
	function disableAlbumForm()
	{
		$('#albumTitleInput').attr("disabled", true);
		$('#albumArtistInput').attr("disabled", true);
		$('#albumLabelInput').attr("disabled", true);
		$('#albumGenreInput').attr("disabled", true);
		$('#albumLocalInput').attr("disabled", true);
		$('#albumCompilationInput').attr("disabled", true);
		$('#albumCDCodeInput').attr("disabled", true);
		$('#albumLocationInput').attr("disabled", true);
		$('#tagsInput').attr("disabled", true);
	}
	
	function enableAlbumForm()
	{
		$('#albumTitleInput').removeAttr("disabled");
		$('#albumArtistInput').removeAttr("disabled");
		$('#albumLabelInput').removeAttr("disabled");
		$('#albumGenreInput').removeAttr("disabled");
		$('#albumLocalInput').removeAttr("disabled");
		$('#albumCompilationInput').removeAttr("disabled");
		$('#albumCDCodeInput').removeAttr("disabled");
		$('#albumLocationInput').removeAttr("disabled");
		$('#tagsInput').removeAttr("disabled");
	}

	function clearAlbumForm()
	{
		$('#albumTitleInput').val('');
		$('#albumArtistInput').val('');
		$('#albumLabelInput').val('');
		$('#albumGenreInput').val('Unknown');
		$('#albumLocalInput').removeAttr('checked');
		$('#albumCompilationInput').removeAttr('checked')
		$('#albumCDCodeInput').val('');
		$('#albumLocationInput').val('GNU Bin');
		$('#tagsInput').val('');
		$("#hiddenTagsInput").ptags_remove($("#hiddenTagsInput").val().split(','));
	}

	function disableTrackForm()
	{
		$('#trackTitleInput').attr("disabled", true);
		$('#trackTrackNumberInput').attr("disabled", true);
		$('#trackDiskNumberInput').attr("disabled", true);
		$('#trackArtistInput').attr("disabled", true);
		$('#trackTimeInput').attr("disabled", true);
	}

	function enableTrackForm()
	{
		$('#trackTitleInput').removeAttr("disabled");
		$('#trackTrackNumberInput').removeAttr("disabled");
		$('#trackDiskNumberInput').removeAttr("disabled");
		$('#trackArtistInput').removeAttr("disabled");
		$('#trackTimeInput').removeAttr("disabled");
	}

	function clearTrackForm()
	{
		$('#trackTitleInput').val('');
		$('#trackTrackNumberInput').val('');
		$('#trackDiskNumberInput').val('');
		$('#trackArtistInput').val('');
		$('#trackTimeInput').val('');
	}
	</script>


<?php ###################################################################### ?>
<?php $body = new BodyTemplateSection();                                   # ?>
<?php $body->write();                                                      # ?>
<?php ###################################################################### ?>

	<table id="albums" style="display: none;"></table>
	<table id="tracks" style="display: none;"></table>
	
	<div id="dialogAddAlbum" class="dialog" style="display:none">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr><td>Artist:</td><td>&nbsp;</td><td><input type="text" name="albumArtist" id="albumArtistInput" size="30" /></td></tr>
				<tr><td>Title:</td><td>&nbsp;</td><td><input type="text" name="albumTitle" id="albumTitleInput" size="30" /></td></tr>
				<tr><td>Label:</td><td>&nbsp;</td><td><input type="text" name="albumLabel" id="albumLabelInput" size="30" /></td></tr>
				<tr><td>Main Genre:</td><td>&nbsp;</td><td>
					<select name="albumGenre" id="albumGenreInput">
						<?php foreach(DB::getInstance('MySql')->find(new Genre(array('TopLevel' => true)), $count, array('sortcolumn' => 'Name')) as $genre): ?>
						<option <?php echo "value=\"$genre->GenreID\"" . ($genre->Name == 'Unknown' ? ' selected="true"' : '') ?>><?php echo $genre->Name ?></option>
						<?php endforeach; ?>
					</select>
				</td></tr>
				<tr><td>Sub-Genres:</td><td>&nbsp;</td><td id="tagsColumn">
					<input id="tagsInput" name="tags" type="text" value="" size="30" /><br />
					<input id="hiddenTagsInput" name="tags" type="text" value="" size="30" style="display:none" />
				</td></tr>
				<tr><td>Local:</td><td>&nbsp;</td><td><input type="checkbox" value="true" name="albumLocal" id="albumLocalInput" /></td></tr>
				<tr><td>Compilation:</td><td>&nbsp;</td><td><input type="checkbox" value="true" name="albumCompilation" id="albumCompilationInput" /></td></tr>
				<tr><td>CD Code:</td><td>&nbsp;</td><td><input type="text" name="albumCDCode" id="albumCDCodeInput" size="6" maxlength="6" /></td></tr>
				<tr><td>Location:</td><td>&nbsp;</td><td>
					<select name="albumLocation" id="albumLocationInput">
						<option value="Digital Library">Digital Library</option>
						<option value="GNU Bin" selected="true">GNU Bin</option>
						<option value="Library">Library</option>
						<option value="Personal">Personal</option>
					</select>
				</td></tr>
				<tr><td></td><td><input style="display:none" type="text" name="albumID" id="albumIDInput" /></td></tr>
			</table>
		<form id="formAlbum" onsubmit="submitAddAlbum(); return false;">
			<input type="submit" value="Submit"/>
		</form>
	</div>

	<div id="dialogAddTrack" class="dialog" style="display:none">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr><td>Title:</td><td>&nbsp;</td><td><input type="text" name="title" id="trackTitleInput" size="30" /></td></tr>
				<tr><td>Track Number:</td><td>&nbsp;</td><td><input type="text" name="trackNumber" id="trackTrackNumberInput" size="3" maxlength="3" /></td></tr>
				<tr><td>Disc Number:</td><td>&nbsp;</td><td><input type="text" name="DiskNumber" id="trackDiskNumberInput" size="3" maxlength="3" /></td></tr>
				<tr><td>Time:</td><td>&nbsp;</td><td><input type="text" name="Time" id="trackTimeInput" size="3" maxlength="5" /></td></tr>
				<tr><td>Artist:</td><td>&nbsp;</td><td><input type="text" name="artist" id="trackArtistInput" size="30" /></td></tr>
				<tr><td></td><td><input style="display:none" type="text" name="trackID" id="trackIDInput" /></td></tr>
			</table>
		<form id="formTrack" onsubmit="submitAddTrack(); return false;">
			<input type="submit" value="Submit"/>
		</form>
	</div>
	
	<div id="dialogDeleteAlbum" class="dialog" style="display:none">
		Are you sure you want to delete this album? All the tracks belonging to it will be deleted as well.
		<form id="formDeleteAlbum" onsubmit="submitDeleteAlbum(); return false;">
			<input type="submit" value="Submit"/>
		</form>
	</div>
	
	<div id="dialogDeleteTrack" class="dialog" style="display:none">
		Are you sure you want to delete this track?
		<form id="formDeleteTrack" onsubmit="submitDeleteTrack(); return false;">
			<input type="submit" value="Submit"/>
		</form>
	</div>
	
	<script type="text/javascript">
		// Create the dialogs
		$('#dialogAddTrack').dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			close: clearTrackForm
		});
			
		$('#dialogDeleteTrack').dialog({
			autoOpen: false,
			title: 'Delete Track'
		});

		$('#dialogAddAlbum').dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			close: clearAlbumForm
		});
		
		$('#dialogDeleteAlbum').dialog({
			autoOpen: false,
			title: 'Delete Album'
		});
	</script>

<?php ###################################################################### ?>
<?php $close = new CloseTemplateSection();                                 # ?>
<?php $close->write();                                                     # ?>
<?php ###################################################################### ?>
