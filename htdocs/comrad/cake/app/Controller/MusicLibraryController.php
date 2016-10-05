<?php
class MusicLibraryController extends AppController {
	var $name = 'MusicLibrary';
	var $uses = array('Album', 'Track', 'Genre');
	var $helpers = array('Time', 'Html', 'Paginator');
	var $components = array('ITunes');
	var $paginate = array(
		'limit' => 20,
		'order' => 'Album.a_AddDate desc',
		'paramType' => 'querystring'
	);
	
	function index() {
		$this->Crumb->saveCrumb('Music Library', $this->request, true);
		
		$conditions = array();
		
		if (isset($this->request->query['q'])) {
			preg_match_all('/"[^"]+"|[\w\']+/', $this->request->query['q'], $q);
			
			if (count($q = $q[0]) > 0) {
				$conditions['AND'] = array();
				
				for ($i = 0; $i < count($q); $i++) {
					$q[$i] = str_replace('"', '', $q[$i]);
					array_push($conditions['AND'], array('OR' => array(
						'Album.a_Title LIKE' => '%'.$q[$i].'%',
						'Album.a_Artist LIKE' => '%'.$q[$i].'%',
						'Genre.g_Name LIKE' => '%'.$q[$i].'%'
					)));
				}
			}
		}
		
		$this->set('albums', $this->paginate('Album', $conditions, array('a_Title', 'a_Artist', 'Genre.g_Name', 'a_AddDate')));
		// $this->set('genres', $this->Genre->find('list', array('conditions' => array('g_TopLevel' => 1), 'order' => 'Genre.g_Name')));
	}
}
?>
