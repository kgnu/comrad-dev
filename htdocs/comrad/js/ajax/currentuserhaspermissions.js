function currentUserHasPermissions(operations, objects, callback) {
	$.get('ajax/currentuserhaspermissions.php', { operations: operations, objects: objects}, function(hasPermission) {
		callback(hasPermission);
	});
}
