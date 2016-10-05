<?php
class ShowsController extends AppController {
	var $name = 'Shows';
	var $uses = array('ShowEvent', 'Host');
	
	function index() {
		$this->set('shows', $this->ShowEvent->find('all', array('order' => 'e_Title ASC')));
	}
	
	function view($id) {
		$show = $this->ShowEvent->find('first', array('conditions' => array('e_Id' => $id)));
		if (!$show) throw new NotFoundException('Show does not exist');
		$this->set('show', $show);
	}
	
	function add() {
		if ($this->request->is('post')) {
			if ($this->ShowEvent->save($this->request->data)) {
				$this->Session->setFlash(
					'The show was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'shows', 'action' => 'view', $this->ShowEvent->id)
					), 'success'
				);
			}
		}
		$this->set('hosts', $this->Host->find('list', array('conditions' => array('Active' => true))));
	}
	
	function edit($id) {
		if ($this->request->is('put')) {
			if ($this->ShowEvent->save($this->request->data)) {
				$this->Session->setFlash(
					'The show was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'shows', 'action' => 'view', $this->ShowEvent->id)
					), 'success'
				);
			}
		} else {
			$this->ShowEvent->id = $id;
			$this->data = $this->ShowEvent->read();
		}
		$this->set('hosts', $this->Host->find('list', array('conditions' => array('Active' => true))));
	}
	
	function api_index() {
		parent::api_index($this->ShowEvent);
	}
	
	function api_view($id) {
		parent::api_view($this->ShowEvent, $id);
	}
}
?>
