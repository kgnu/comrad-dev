// Documentation at http://www.apple.com/itunes/affiliates/resources/documentation/itunes-store-web-service-search-api.html
function searchITunes(params, callback) {
	$.ajax({
		method: 'GET',
		url: 'http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/wa/wsSearch',
		dataType: 'jsonp',
		data: params,
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
        method: 'GET',
        url: 'http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/wa/wsLookup',
        dataType: 'jsonp',
		data: { id: iTunesAlbumId },
		success: function(response) {
		    if (response.results.length > 0) {
    			if (callback) callback(response.results[0]);
		    }
		}
    });
}