<?php
class ArtistsController extends AppController {
	var $name = 'Artists';
	var $uses = array('Album', 'Track');
	// var $helpers = array('Time');
	// var $components = array('ITunes');
	
	function index() {
		// if (isset($this->data['keyword'])) {
		// 	$this->set('albums', $this->Album->findByKeyword($this->data['keyword'], array('order' => 'Album.a_AddDate DESC')));
		// } else {
		// 	$this->set('albums', $this->Album->find('all', array('order' => 'Album.a_AddDate DESC')));
		// }
	}
	
	function view($artistName = null) {
		if ($artistName === null) $this->cakeError('error404');
		
		$albums = $this->Album->find('all', array('conditions' => array('Album.a_Artist' => $artistName)));
		$tracks = $this->Track->find('all', array('conditions' => array('Track.t_Artist' => $artistName)));
		
		if (!$albums && !$tracks) $this->cakeError('error404');
		
		$this->set('artistName', $artistName);
		$this->set('albums', $albums);
		$this->set('tracks', $tracks);
	}
}
?>
