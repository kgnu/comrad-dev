<?php
class ScheduledEventsController extends AppController {
	var $name = 'ScheduledEvents';
	var $uses = array('ScheduledEvent', 'Event');
	
	function index() {
		$this->set('scheduledEvents', $this->ScheduledEvent->find('all'));
	}
	
	function view($id) {
		
	}
	
	function add() {
		if ($this->request->is('post')) {
			if ($this->ScheduledEvent->saveAssociated($this->request->data, array('validate' => true))) {
				$this->Session->setFlash(
					'The scheduled event was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'scheduled_events', 'action' => 'view', $this->ScheduledEvent->id)
					), 'success'
				);
			}
		}
		$this->set('events', $this->Event->find('list', array(
			'conditions' => array('e_Active' => true),
			'order' => 'e_Title ASC'
		)));
	}
	
	function edit($id) {
		
	}
	
	function delete($id) {
		
	}
	
	function api_index() {
		parent::api_index($this->ScheduledEvent);
	}
	
	function api_view($id) {
		parent::api_view($this->ScheduledEvent, $id);
	}
}
?>
