<?php
class RecurringShowException extends AbstractDBObject
{
	public function __construct($params = array())
	{
		$this->columns = array(
			'ID' => array(
				'type' => 'PrimaryKey'
			),
			'TimeInfoID' => array( // Must be one-time
				'type' => 'ForeignKey',
				'foreignType' => 'TimeInfo',
				'required' => true
			),
			'TimeInfo' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'TimeInfo',
				'localcolumn' => 'TimeInfoID',
				'foreigncolumn' => 'ID'
			),
			'RecurringShowID' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'TimeInfo',
				'required' => true
			),
			'RecurringShow' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'RecurringShow',
				'localcolumn' => 'RecurringShowID',
				'foreigncolumn' => 'ID'
			)
		);
		
		parent::__construct($params);
	}
	 
	public function getTableName()
	{
		return "RecurringShowException";
	}
	 
	public function __toString()
	{
		return 'RecurringShowException #'.$this->ID;
	}
}
?>