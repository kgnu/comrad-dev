<?php
class PSAsController extends AppController {
	var $name = 'PSAs';
	var $uses = array('PSAEvent', 'PSACategory');
	
	function index() {
		$this->set('psas', $this->PSAEvent->find('all', array('order' => 'e_Title ASC')));
	}
	
	function view($id) {
		$psa = $this->PSAEvent->find('first', array('conditions' => array('e_Id' => $id)));
		if (!$psa) throw new NotFoundException('PSA does not exist');
		$this->set('psa', $psa);
	}
	
	function add() {
		if ($this->request->is('post')) {
			if ($this->PSAEvent->save($this->request->data)) {
				$this->Session->setFlash(
					'The PSA was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'p_s_as', 'action' => 'view', $this->PSAEvent->id)
					), 'success'
				);
			}
		}
		$this->set('psaCategories', $this->PSACategory->find('list', array('order' => 'Title ASC')));
	}
	
	function edit($id) {
		if ($this->request->is('put')) {
			if ($this->PSAEvent->save($this->request->data)) {
				$this->Session->setFlash(
					'The PSA was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'p_s_as', 'action' => 'view', $this->PSAEvent->id)
					), 'success'
				);
			}
		} else {
			$this->PSAEvent->id = $id;
			$this->data = $this->PSAEvent->read();
		}
		$this->set('psaCategories', $this->PSACategory->find('list', array('order' => 'Title ASC')));
	}
	
	function api_index() {
		parent::api_index($this->PSAEvent);
	}
	
	function api_view($id) {
		parent::api_view($this->PSAEvent, $id);
	}
}
?>
