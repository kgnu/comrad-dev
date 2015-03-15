function musicCatalogKeywordSearch(params, callback) {
	$.ajax({
		method: 'GET',
		url: 'ajax/musickeywordsearch.php',
		dataType: 'json',
		data: params,
		success: function(response) {
			if (callback) callback(response);
		}
	});
}

function findTracksInAlbum(params, callback) {
    $.ajax({
        method: 'GET',
        url: 'ajax/gettracksinalbum.php',
        dataType: 'json',
        data: params,
        success: function(response) {
            if (callback) callback(response);
        }
    })
}