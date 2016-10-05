<?php
class ScheduledEventInstancesController extends AppController {
	var $name = 'ScheduledEventInstances';
	var $uses = array('ScheduledEventInstance', 'ScheduledEvent', 'Event');
	
	function index() {
		$this->set('scheduledEventInstances', $this->ScheduledEventInstance->find('all'));
	}
	
	function api_index() {
		parent::api_index($this->ScheduledEventInstance);
	}
	
	function api_view($id) {
		parent::api_view($this->ScheduledEventInstance, $id);
	}
}
?>
