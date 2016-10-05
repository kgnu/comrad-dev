<?php
class AnnouncementsController extends AppController {
	var $name = 'Announcements';
	var $uses = array('AnnouncementEvent');
	
	function index() {
		$this->set('announcements', $this->AnnouncementEvent->find('all', array('order' => 'e_Title ASC')));
	}
	
	function view($id) {
		$announcement = $this->AnnouncementEvent->find('first', array('conditions' => array('e_Id' => $id)));
		if (!$announcement) throw new NotFoundException('Announcement does not exist');
		$this->set('announcement', $announcement);
	}
	
	function add() {
		if ($this->request->is('post')) {
			if ($this->AnnouncementEvent->save($this->request->data)) {
				$this->Session->setFlash(
					'The announcement was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'announcements', 'action' => 'view', $this->AnnouncementEvent->id)
					), 'success'
				);
			}
		}
	}
	
	function edit($id) {
		if ($this->request->is('put')) {
			if ($this->AnnouncementEvent->save($this->request->data)) {
				$this->Session->setFlash(
					'The announcement was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'announcements', 'action' => 'view', $this->AnnouncementEvent->id)
					), 'success'
				);
			}
		} else {
			$this->AnnouncementEvent->id = $id;
			$this->data = $this->AnnouncementEvent->read();
		}
	}
	
	function api_index() {
		parent::api_index($this->AnnouncementEvent);
	}
	
	function api_view($id) {
		parent::api_view($this->AnnouncementEvent, $id);
	}
}
?>
