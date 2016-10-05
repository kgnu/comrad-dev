<?php
App::uses('HttpSocket', 'Network/Http');

class ITunesComponent extends Component {
	function searchAlbums($term) {
		$httpSocket = new HttpSocket();
		return json_decode($httpSocket->get('http://itunes.apple.com/search?term=' . $term . '&entity=album&limit=60'), true);
	}
	
	function getAlbumById($albumId) {
		$httpSocket = new HttpSocket();
		return json_decode($httpSocket->get('http://itunes.apple.com/lookup?id=' . $albumId . '&entity=song&limit=500'), true);
	}
	
	function getAlbumsByArtistId($artistId) {
		$httpSocket = new HttpSocket();
		return json_decode($httpSocket->get('http://itunes.apple.com/lookup?id=' . $artistId . '&entity=album'), true);
	}
}
?>
