<?php
class LegalIDsController extends AppController {
	var $name = 'LegalIDs';
	var $uses = array('LegalIDEvent');
	
	function index() {
		$this->set('legalIDs', $this->LegalIDEvent->find('all', array('order' => 'e_Title ASC')));
	}
	
	function view($id) {
		$legalID = $this->LegalIDEvent->find('first', array('conditions' => array('e_Id' => $id)));
		if (!$legalID) throw new NotFoundException('Legal ID does not exist');
		$this->set('legalID', $legalID);
	}
	
	function add() {
		if ($this->request->is('post')) {
			if ($this->LegalIDEvent->save($this->request->data)) {
				$this->Session->setFlash(
					'The legal ID was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'legal_i_ds', 'action' => 'view', $this->LegalIDEvent->id)
					), 'success'
				);
			}
		}
	}
	
	function edit($id) {
		if ($this->request->is('put')) {
			if ($this->LegalIDEvent->save($this->request->data)) {
				$this->Session->setFlash(
					'The legal ID was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'legal_i_ds', 'action' => 'view', $this->LegalIDEvent->id)
					), 'success'
				);
			}
		} else {
			$this->LegalIDEvent->id = $id;
			$this->data = $this->LegalIDEvent->read();
		}
	}
	
	function api_index() {
		parent::api_index($this->LegalIDEvent);
	}
	
	function api_view($id) {
		parent::api_view($this->LegalIDEvent, $id);
	}
}
?>
