<?php
class PSACategoriesController extends AppController {
	var $name = 'PSACategories';
	var $uses = array('PSACategory');
	
	function index() {
		$this->set('psaCategories', $this->PSACategory->find('all', array('order' => 'Title ASC')));
	}
	
	function view($id) {
		$psaCategory = $this->PSACategory->find('first', array('conditions' => array('Id' => $id)));
		if (!$psaCategory) throw new NotFoundException('PSA Category does not exist');
		$this->set('psaCategory', $psaCategory);
	}
	
	function add() {
		if ($this->request->is('post')) {
			if ($this->PSACategory->save($this->request->data)) {
				$this->Session->setFlash(
					'The PSA category was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'p_s_a_categories', 'action' => 'view', $this->PSACategory->id)
					), 'success'
				);
			}
		}
	}
	
	function edit($id) {
		if ($this->request->is('put')) {
			if ($this->PSACategory->save($this->request->data)) {
				$this->Session->setFlash(
					'The PSA category was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'p_s_a_categories', 'action' => 'view', $this->PSACategory->id)
					), 'success'
				);
			}
		} else {
			$this->PSACategory->id = $id;
			$this->data = $this->PSACategory->read();
		}
	}
	
	function api_index() {
		parent::api_index($this->PSACategory);
	}
	
	function api_view($id) {
		parent::api_view($this->PSACategory, $id);
	}
}
?>
