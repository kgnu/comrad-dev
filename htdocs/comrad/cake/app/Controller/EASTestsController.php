<?php
class EASTestsController extends AppController {
	var $name = 'EASTests';
	var $uses = array('EASTestEvent');
	
	function index() {
		$this->set('easTests', $this->EASTestEvent->find('all', array('order' => 'e_Title ASC')));
	}
	
	function view($id) {
		$easTest = $this->EASTestEvent->find('first', array('conditions' => array('e_Id' => $id)));
		if (!$easTest) throw new NotFoundException('EAS Test does not exist');
		$this->set('easTest', $easTest);
	}
	
	function add() {
		if ($this->request->is('post')) {
			if ($this->EASTestEvent->save($this->request->data)) {
				$this->Session->setFlash(
					'The EAS Test was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'e_a_s_tests', 'action' => 'view', $this->EASTestEvent->id)
					), 'success'
				);
			}
		}
	}
	
	function edit($id) {
		if ($this->request->is('put')) {
			if ($this->EASTestEvent->save($this->request->data)) {
				$this->Session->setFlash(
					'The EAS Test was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'e_a_s_tests', 'action' => 'view', $this->EASTestEvent->id)
					), 'success'
				);
			}
		} else {
			$this->EASTestEvent->id = $id;
			$this->data = $this->EASTestEvent->read();
		}
	}
	
	function api_index() {
		parent::api_index($this->EASTestEvent);
	}
	
	function api_view($id) {
		parent::api_view($this->EASTestEvent, $id);
	}
}
?>
