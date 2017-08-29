<?php
	//proxy for pulling data from itunes since the itunes SSL certificate is invalid so we can't access https
	//and, if we access http, it causes mixed content warnings
	$url = 'http://ax.itunes.apple.com/WebObjects/MZStoreServices.woa/wa/' . $_POST['action'];
	$url .= '?' . http_build_query($_POST['parameters']);
	echo file_get_contents($url);
?>