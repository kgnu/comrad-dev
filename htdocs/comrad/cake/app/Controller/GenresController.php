<?php
class GenresController extends AppController {
	var $name = 'Genres';
	
	function index() {
		$this->set('genres', $this->Genre->find('all'));
	}
	
	function create() {
		
	}
	
	function edit() {
		
	}
	
	function delete() {
		
	}
	
	function save() {
		
	}
}
?>
