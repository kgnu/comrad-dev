<?php
class Track extends AbstractDBObject
{
	public function __construct($params = array())
	{
		global $init;
		
		$this->columns = array(
			'TrackID' => array(
				'type' => 'PrimaryKey'
			),
			'AlbumID' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'Album',
				'required' => true
			),
			'Title' => array(
				'type' => 'UppercaseString',
				'required' => true,
				'titlecolumn' => true
			),
			'TrackNumber' => array(
				'type' => 'Integer',
				'required' => true,
				'tostring' => 'Track Number'
			),
			'Artist' => array(
				'type' => 'UppercaseString',
				'required' => false
			),
			'DiskNumber' => array(
				'type' => 'Integer',
				'tostring' => 'Disk Number'
			),
			'Duration' => array(
				'type' => 'Integer',
				'required' => $init->getProp('ReportingPeriod'),
				'tostring' => 'Duration (in seconds)'
			),
			'Album' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'Album',
				'localcolumn' => 'AlbumID',
				'foreigncolumn' => 'AlbumID'
			)
		);

		parent::__construct($params);
	}
	
	public function getTableColumnPrefix() {
		return 't_';
	}
	 
	public function getTableName()
	{
		return "Tracks";
	}
	 
	public function __toString()
	{
		return $this->Title;
	}
}
?>
