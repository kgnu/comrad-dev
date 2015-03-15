<?php
class Album extends AbstractDBObject
{
	public function __construct($params = array())
	{
		global $init;
		
		$this->columns = array(
			'AlbumID' => array(
				'type' => 'PrimaryKey'
			),
			'ITunesId' => array(
				'type' => 'Integer',
				'tostring' => 'iTunes ID'
			),
			'Title' => array(
				'type' => 'UppercaseString',
				'required' => true,
				'titlecolumn' => true
			),
			'Artist' => array(
				'type' => 'UppercaseString'
			),
			'Label' => array(
				'type' => 'UppercaseString',
				'required' => $init->getProp('ReportingPeriod'),
			),
			'GenreID' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'Genre',
				'tostring' => 'Genre'
			),
			'Genre' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'Genre',
				'localcolumn' => 'GenreID',
				'foreigncolumn' => 'GenreID'
			),
			'AddDate' => array(
				'type' => 'Date',
				'tostring' => 'Add Date'
			),
			'Local' => array(
				'type' => 'Boolean'
			),
			'Compilation' => array(
				'type' => 'Boolean'
			),
			'Location' => array(
				'type' => 'Enumeration',
				'possiblevalues' => array(
					'GNU Bin',
					'Personal',
					'Library',
					'Digital Library'
				)
			),
			'Tracks' => array(
				'type' => 'ForeignKeyCollection',
				'foreignType' => 'Track',
				'localcolumn' => 'AlbumID',
				'foreigncolumn' => 'AlbumID'
			),
			'AlbumArt' => array(
				'type' => 'String',
				'tostring' => 'Album Art'
			)
		);
		
		parent::__construct($params);
	}
	
	public function getTableColumnPrefix() {
		return 'a_';
	}
	 
	public function getTableName()
	{
		return "Albums";
	}
	 
	public function __toString()
	{
		return $this->Title;
	}
}
?>
