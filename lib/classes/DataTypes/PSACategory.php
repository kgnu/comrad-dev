<?php
class PSACategory extends AbstractDBObject
{
	public function __construct($params = array())
	{
		$this->columns = array(
			'Id' => array(
				'type' => 'PrimaryKey'
			),
			'Title' => array(
				'type' => 'UppercaseString',
				'required' => true,
				'titlecolumn' => true
			)
		);

		parent::__construct($params);
	}
	 
	public function getTableName()
	{
		return "PSACategory";
	}
	 
	public function __toString()
	{
		return $this->Title;
	}
}
?>
