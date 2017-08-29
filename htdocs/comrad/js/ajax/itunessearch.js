// Documentation at http://www.apple.com/itunes/affiliates/resources/documentation/itunes-store-web-service-search-api.html
function searchITunes(params, callback) {
	$.ajax({
		type: 'POST',
		url: 'ajax/itunesproxy.php',
		dataType: 'json',
		data: {
			action: 'wsSearch',
			parameters: params
		},
		success: function(response) {
			var results = [];
			
			$.each(response.results, function(i, result) {
				if (result.kind == 'song') {
					results.push({
						'Type': 'TrackFromITunes',
						'Attributes': {
							'Title': result.trackName,
							'DiskNumber': result.discNumber,
							'TrackNumber': result.trackNumber,
							'Duration': Math.floor(result.trackTimeMillis / 1000),
							'Album': {
								'Type': 'Album',
								'Attributes': {
								    'ITunesCollectionId': result.collectionId,
									'ITunesId': result.collectionId,
									'Title': result.collectionName,
									'Artist': result.artistName,
									'AlbumArt': result.artworkUrl60,
									'Genre': {
									    'Type': 'Genre',
									    'Attributes': {
									        'Name': result.primaryGenreName,
									        'TopLevel': true
								        }
							        }
								}
							}
						}
					});
				}
			});
			
			if (callback) callback(results);
		}
	});
}

function getITunesAlbumInfo(iTunesAlbumId, callback) {
    $.ajax({
		type: 'POST',
		url: 'ajax/itunesproxy.php',
        dataType: 'json',
		data: { 
			action: 'wsLookup',
			parameters: { id: iTunesAlbumId }
		},
		success: function(response) {
		    if (response.results.length > 0) {
    			if (callback) callback(response.results[0]);
		    }
		}
    });
}