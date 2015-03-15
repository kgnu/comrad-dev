<?php
class GenreTag extends AbstractDBObject
{
	public function __construct($params = array()) 
	{
		$this->columns = array(
			'GenreTagID' => array(
				'type' => 'PrimaryKey'
			),
			'GenreID' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'Genre',
				'required' => true,
				'tostring' => 'Genre'
			),
			'AlbumID' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'Album',
				'required' => true,
				'tostring' => 'Album'
			)
		);

		parent::__construct($params);
	}
	
	public function getTableColumnPrefix() {
		return 'gt_';
	}
	 
	public function getTableName()
	{
		return "GenreTags";
	}
	 
	public function __toString()
	{
		return "GenreTag: Genre#$this->GenreID => Album#$this->AlbumID";
	}
}
?>
