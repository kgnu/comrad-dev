<?php
class Genre extends AbstractDBObject
{
	public function __construct($params = array())
	{
		$this->columns = array(
			'GenreID' => array(
				'type' => 'PrimaryKey'
			),
			'Name' => array(
				'type' => 'UppercaseString',
				'required' => true
			),
			'TopLevel' => array(
				'type' => 'Boolean',
				'required' => true,
				'tostring' => 'Top Level'
			)
		);

		parent::__construct($params);
	}
	
	public function getTableColumnPrefix() {
		return 'g_';
	}
	 
	public function getTableName()
	{
		return "Genres";
	}
	 
	public function __toString()
	{
		return $this->Name;
	}
}
?>
