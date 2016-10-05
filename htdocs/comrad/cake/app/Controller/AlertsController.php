<?php
class AlertsController extends AppController {
	var $name = 'Alerts';
	var $uses = array('AlertEvent');
	
	function index() {
		$this->set('alerts', $this->AlertEvent->find('all', array('order' => 'e_Title ASC')));
	}
	
	function view($id) {
		$alert = $this->AlertEvent->find('first', array('conditions' => array('e_Id' => $id)));
		if (!$alert) throw new NotFoundException('Alert does not exist');
		$this->set('alert', $alert);
	}
	
	function add() {
		if ($this->request->is('post')) {
			if ($this->AlertEvent->save($this->request->data)) {
				$this->Session->setFlash(
					'The alert was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'alerts', 'action' => 'view', $this->AlertEvent->id)
					), 'success'
				);
			}
		}
	}
	
	function edit($id) {
		if ($this->request->is('put')) {
			if ($this->AlertEvent->save($this->request->data)) {
				$this->Session->setFlash(
					'The alert was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'alerts', 'action' => 'view', $this->AlertEvent->id)
					), 'success'
				);
			}
		} else {
			$this->AlertEvent->id = $id;
			$this->data = $this->AlertEvent->read();
		}
	}
	
	function api_index() {
		parent::api_index($this->AlertEvent);
	}
	
	function api_view($id) {
		parent::api_view($this->AlertEvent, $id);
	}
}
?>
