<?php
class UnderwritingsController extends AppController {
	var $name = 'Underwritings';
	var $uses = array('UnderwritingEvent');
	
	function index() {
		$this->set('underwritings', $this->UnderwritingEvent->find('all', array('order' => 'e_Title ASC')));
	}
	
	function view($id) {
		$underwriting = $this->UnderwritingEvent->find('first', array('conditions' => array('e_Id' => $id)));
		if (!$underwriting) throw new NotFoundException('Underwriting does not exist');
		$this->set('underwriting', $underwriting);
	}
	
	function add() {
		if ($this->request->is('post')) {
			if ($this->UnderwritingEvent->save($this->request->data)) {
				$this->Session->setFlash(
					'The underwriting was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'underwritings', 'action' => 'view', $this->UnderwritingEvent->id)
					), 'success'
				);
			}
		}
	}
	
	function edit($id) {
		if ($this->request->is('put')) {
			if ($this->UnderwritingEvent->save($this->request->data)) {
				$this->Session->setFlash(
					'The underwriting was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'underwritings', 'action' => 'view', $this->UnderwritingEvent->id)
					), 'success'
				);
			}
		} else {
			$this->UnderwritingEvent->id = $id;
			$this->data = $this->UnderwritingEvent->read();
		}
	}
	
	function api_index() {
		parent::api_index($this->UnderwritingEvent);
	}
	
	function api_view($id) {
		parent::api_view($this->UnderwritingEvent, $id);
	}
}
?>
