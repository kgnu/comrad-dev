<?php
class Album extends AppModel {
	var $name = 'Album';
	
	var $useTable = 'Albums';
	var $primaryKey = 'a_AlbumID';
	var $displayField = 'a_Title';
	
	var $belongsTo = array(
		'Genre' => array(
			'foreignKey' => 'a_GenreID'
		)
	);
	
	var $hasMany = array(
		'Track' => array(
			'className' => 'Track',
			'foreignKey' => 't_AlbumID',
			'order' => 'Track.t_DiskNumber ASC, Track.t_TrackNumber ASC',
			'dependent' => true
		)
	);
	
	var $validate = array(
		'a_AlbumID' => array(
			'numeric' => array(
				'rule' => 'numeric',
				'required' => true,
				'message' => 'CD Code is required'
			),
			'unique' => array( // We disable this when updating, since the album already exists...
				'rule' => '_hasUniqueCDCode',
				'message' => 'An album with this CD Code already exists'
			)
		),
		'a_Title' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => 'Title is required'
		),
		'a_Artist' => array(
			'rule' => '_hasArtistOrIsCompilation',
			'message' => 'Artist is required because the album is not a compilation'
		),
		'a_Label' => array(
			'rule' => 'notEmpty',
			'required' => true,
			'message' => 'Label is required'
		),
		'a_GenreID' => array(
			'rule' => 'numeric',
			'required' => true,
			'message' => 'Genre is required'
		),
		'a_Compilation' => array(
			'rule' => 'boolean',
			'required' => true
		),
		'a_AlbumArt' => array(
			'rule' => 'url',
			'required' => false,
			'allowEmpty' => true,
			'message' => 'This should be a URL (ex: http://example.com/album-cover.jpg)'
		)
	);
	
	function beforeSave() {
		// If the album is a compilation, clear the Artist field
		if ($this->data['Album']['a_Compilation']) {
			$this->data['Album']['a_Artist'] = '';
		}
		
		return true;
	}
	
	function afterSave($created) {
		//update the TrackFullTextSearchInfo with the new data
		if (! $created) {
			$query = "DELETE FROM TrackFullTextSearchInfo 
					  WHERE tftsi_TrackId IN (SELECT t_TrackId FROM Tracks WHERE t_AlbumId = " . $this->getDataSource()->value($this->data['Album']['a_AlbumID']) . ")";
			$this->query($query);
		}
		
		$query = "INSERT INTO TrackFullTextSearchInfo 
			(tftsi_TrackId, tftsi_TrackArtist, tftsi_TrackTitle, 
			tftsi_AlbumArtist, tftsi_AlbumLabel, tftsi_AlbumTitle,
			tftsi_GenreName)
		SELECT
			t.t_TrackId, t.t_Artist, t.t_Title,
			a.a_Artist, a.a_Label, a.a_Title,
			g.g_Name
		FROM
			Tracks AS t
			LEFT JOIN Albums as a ON t.t_AlbumId = a.a_AlbumId
			LEFT JOIN Genres as g ON a.a_GenreId = g.g_GenreId
		WHERE t.t_AlbumId = " . $this->getDataSource()->value($this->data['Album']['a_AlbumID']);
		$this->query($query);
	}
	
	function afterDelete() {
		$query = "DELETE FROM TrackFullTextSearchInfo 
				  WHERE tftsi_TrackId IN (SELECT t_TrackId FROM Tracks WHERE t_AlbumId = " . $this->getDataSource()->value($this->id) . ")";
		$this->query($query);
	}
	
	// Require artist unless the album is a compilation
	protected function _hasArtistOrIsCompilation() {
		return ($this->data['Album']['a_Compilation'] || !empty($this->data['Album']['a_Artist']));
	}
	
	protected function _hasUniqueCDCode() {
		return (count($this->find('list', array('conditions' => array('a_AlbumID' => $this->data['Album']['a_AlbumID'])))) === 0);
	}
	
	public function findByKeyword($keyword, $params = array()) {
		$conditions = array(
			'OR' => array(
				'Album.a_Title LIKE' => '%'.$keyword.'%',
				'Album.a_Artist LIKE' => '%'.$keyword.'%'
			)
		);
		
		if (isset($params['conditions'])) $conditions = array_merge_recursive($params['conditions'], $conditions);
		
		return $this->find('all', array_merge($params, array('conditions' => $conditions)));
	}
}
?>
