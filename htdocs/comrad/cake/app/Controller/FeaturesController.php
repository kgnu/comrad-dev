<?php
class FeaturesController extends AppController {
	var $name = 'Features';
	var $uses = array('FeatureEvent');
	
	function index() {
		$this->set('features', $this->FeatureEvent->find('all', array('order' => 'e_Title ASC')));
	}
	
	function view($id) {
		$feature = $this->FeatureEvent->find('first', array('conditions' => array('e_Id' => $id)));
		if (!$feature) throw new NotFoundException('Feature does not exist');
		$this->set('feature', $feature);
	}
	
	function add() {
		if ($this->request->is('post')) {
			if ($this->FeatureEvent->save($this->request->data)) {
				$this->Session->setFlash(
					'The feature was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'features', 'action' => 'view', $this->FeatureEvent->id)
					), 'success'
				);
			}
		}
	}
	
	function edit($id) {
		if ($this->request->is('put')) {
			if ($this->FeatureEvent->save($this->request->data)) {
				$this->Session->setFlash(
					'The feature was saved.',
					'flash_success',
					array(
						'link_text' => 'View now',
						'link_url' => array('controller' => 'features', 'action' => 'view', $this->FeatureEvent->id)
					), 'success'
				);
			}
		} else {
			$this->FeatureEvent->id = $id;
			$this->data = $this->FeatureEvent->read();
		}
	}
	
	function api_index() {
		parent::api_index($this->FeatureEvent);
	}
	
	function api_view($id) {
		parent::api_view($this->FeatureEvent, $id);
	}
}
?>
