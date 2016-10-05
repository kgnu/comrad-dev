<?php
class TracksController extends AppController {
	var $name = 'Tracks';
	var $uses = array('Track', 'Album');
	
	function index() {
		$this->set('tracks', $this->Track->find('all'));
	}
	
	function view($id) {
		$track = $this->Track->find('first', array('conditions' => array('t_TrackID' => $id), 'recursive' => 2));
		if (!$track) throw new NotFoundException('Track does not exist');
		
		$this->Crumb->saveCrumb($track['Track']['t_Title'], $this->request);
		
		$this->set('track', $track);
	}
	
	function add($albumId) {
		// Make sure the album exists
		$album = $this->Album->find('first', array('conditions' => array('Album.a_AlbumID' => $albumId)));
		if (!$album) throw new NotFoundException();
		
		$this->Crumb->saveCrumb('Add Track', $this->request);
		
		$this->set('album', $album);
		
		if ($this->request->is('put')) {
			if ($this->Track->save($this->request->data)) {
				$this->Session->setFlash(
					'The track was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'tracks', 'action' => 'view', $this->Track->id)
					), 'success'
				);
				// $this->redirect(array('controller' => 'tracks', 'action' => 'add', $albumId));
			}
		}
	}
	
	function edit($id) {
		if ($this->request->is('put') && !empty($this->request->data)) {
			if ($this->Track->save($this->request->data)) {
				$this->Session->setFlash(
					'The track was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'tracks', 'action' => 'view', $this->Track->id)
					), 'success'
				);
				$this->redirect(array('controller' => 'tracks', 'action' => 'edit', $id));
			}
		} else {
			$this->Track->id = $id;
			$this->data = $this->Track->read();
			$this->Crumb->saveCrumb($this->data['Track']['t_Title'], $this->request);
		}
		
		$track = $this->Track->find('first', array('conditions' => array('Track.t_TrackID' => $id)));
		$this->set('track', $track);
		$this->set('album', $this->Album->find('first', array('conditions' => array('Album.a_AlbumID' => $track['Track']['t_AlbumID']))));
	}
	
	function delete($id) {
		$track = $this->Track->find('first', array('conditions' => array('t_TrackID' => $id)));
		if (count($track['FloatingShowElement']) > 0) {
			$this->set('track', $track);
			$this->set('deleteDenied', true);
		} else {
			if ($this->request->is('delete') && !empty($this->request->data)) {
				if ($this->request->data['confirm']) {
					if ($this->Track->delete($this->request->data['t_TrackID'])) {
						$this->Session->setFlash('The track was successfully deleted.');
						$this->redirect(array('controller' => 'albums', 'action' => 'view', $track['Track']['t_AlbumID']));
					}
				} else {
					$this->redirect(array('controller' => 'tracks', 'action' => 'view', $this->request->data['t_TrackID']));
				}
			} else {
				$this->set('trackId', $id);
			}
		}
	}
	
	function save() {
		if (!empty($this->data)) {
			if ($this->Track->save($this->data)) {
				$this->Session->setFlash('The Track was successfully saved.');
				$this->redirect(array('controller' => 'Albums', 'action' => 'view', $this->data['Track']['t_AlbumID']));
			}
		}
	}
	
	function api_index() {
		parent::api_index($this->Track);
	}
	
	function api_view($id) {
		parent::api_view($this->Track, $id);
	}
}
?>
