<?php
	//proxy for pulling data from itunes since the itunes SSL certificate is invalid so we can't access https
	//and, if we access http, it causes mixed content warnings
	$parameters = $_POST['parameters'];
	if (!is_array($parameters) && !is_object($parameters)) {
		$parameters = array();
	}
	$url = 'http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/wa/' . $_POST['action'];
	$url .= '?' . http_build_query($parameters);
	echo file_get_contents($url);
?>