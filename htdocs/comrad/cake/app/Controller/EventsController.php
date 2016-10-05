<?php
class EventsController extends AppController {
	var $name = 'Events';
	var $uses = array('Event', 'Host');
	
	function index() {
	}
	
	function api_index() {
		parent::api_index($this->Event);
	}
	
	function api_view($id) {
		parent::api_view($this->Event, $id);
	}
}
?>
