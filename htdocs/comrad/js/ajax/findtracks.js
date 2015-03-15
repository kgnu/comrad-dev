function findTracksFromCatalogOrITunes(q, offset, callback) {
	
	// Get catalog results
	
	var localResults;
	var iTunesResults;
	
	var resultsPerPage = 30;
	
	musicCatalogKeywordSearch({
		'q': q,
		'limit': 200
	}, function(results) {
		localResults = results;
		
		if (iTunesResults !== undefined) {
			for (var i = 0; i < iTunesResults.length; i++) {
				localResults.push(iTunesResults[i]);
			}
			
			callback(localResults.splice(offset, Math.min(30, localResults.length - offset)));
		}
	});
	
	searchITunes({
		'term': q,
		'limit': 200,
		'media': 'music',
		'entity': 'musicTrack'
	}, function(results) {
		iTunesResults = results;
		
		if (localResults !== undefined) {
			for (var i = 0; i < iTunesResults.length; i++) {
				localResults.push(iTunesResults[i]);
			}
			
			callback(localResults.splice(offset, Math.min(30, localResults.length - offset)));
		}
	});
	
	// If necessary, get iTunes results
}