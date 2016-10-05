<?php
class ScheduleController extends AppController {
	var $name = 'Schedule';
	var $uses = array('Album', 'Track', 'Genre');
	var $helpers = array('Time', 'Html');
	
	function index() {
		$this->Crumb->saveCrumb('Schedule', $this->request, true);
		
		$this->set('events', array());
	}
}
?>
