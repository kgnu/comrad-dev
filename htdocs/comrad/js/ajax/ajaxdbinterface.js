var verbMapping = {
	'get': 'GET',
	'find': 'GET',
	'save': 'POST',
	'insert': 'POST',
	'update': 'POST',
	'delete': 'POST'
};

function dbCommand(method, type, db, attributes, options, callback) {
	$.ajax({
		type: verbMapping[method],
		url: 'ajax/ajaxdbinterface.php',
		dataType: 'json',
		data: {
			method: method,
			db: db,
			params: $.toJSON({
				Type: type,
				Attributes: attributes,
				Options: options
			})
		},
		success: function(response) {
			callback(response);
		}
	});
}

function dbCommandCriteria(method, type, db, criteria, options, callback) {
	$.ajax({
		type: verbMapping[method],
		url: 'ajax/ajaxdbinterface.php',
		dataType: 'json',
		data: {
			method: method,
			db: db,
			params: $.toJSON({
				Type: type,
				Criteria: criteria,
				Options: options
			})
		},
		success: function(response) {
			callback(response);
		}
	});
}

function dbCommandMultiple(method, queries, callback) {
	$.ajax({
		type: verbMapping[method],
		url: 'ajax/ajaxdbinterface.php',
		dataType: 'json',
		data: {
			queries: $.toJSON(queries)
		},
		success: function(response) {
			callback(response);
		}
	});
}

// Search for the 'find' object, and if it isn't found, insert the 'insert' object
function findOrInsertDifferentDBObjects(type, db, findAttributes, insertAttributes, callback) {
	dbCommand('find', type, db, findAttributes, {}, function(response) {
		if (response && !response.error) {
			if (response.length > 0) {
				if (callback) callback(response[0]); // Successfully found it
			} else {
				dbCommand('insert', type, db, insertAttributes, {}, function(response) {
					if (callback) callback(response);
				});
			}
		} else {
			if (callback) callback(response);
		}
	});
}

// If the object with the specified attributes isn't found
function findOrInsertDBObject(type, db, attributes, callback) {
	findOrInsertDifferentDBObjects(type, db, attributes, attributes, callback);
}

