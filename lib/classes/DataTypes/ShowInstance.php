<?php
class ShowInstance extends AbstractDBObject
{
	public function __construct($params = array())
	{
		$this->columns = array(
			'ID' => array(
				'type' => 'PrimaryKey'
			),
			'TimeInfoID' => array( // Must be one-time (non-repeating)
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
				'foreignType' => 'RecurringShow',
				'required' => true
			),
			'RecurringShow' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'RecurringShow',
				'localcolumn' => 'RecurringShowID',
				'foreigncolumn' => 'ID'
			),
			'RecurringShowExceptionID' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'RecurringShowException',
				'required' => true
			),
			'RecurringShowException' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'RecurringShowException',
				'localcolumn' => 'RecurringShowExceptionID',
				'foreigncolumn' => 'ID'
			),
			'HostID' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'Host',
				'required' => true,
				'tostring' => 'Host'
			),
			'Host' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'Host',
				'localcolumn' => 'HostID',
				'foreigncolumn' => 'UID'
			),
			'DescriptionShort' => array(
				'type' => 'String',
				'tostring' => 'Short Description'
			),
			'DescriptionLong' => array(
				'type' => 'String',
				'tostring' => 'Long Description'
			)
		);
		
		parent::__construct($params);
	}
	 
	public function getTableName()
	{
		return "ShowInstance";
	}
	 
	public function __toString()
	{
		return 'ShowInstance #'.$this->ID;
	}
}
?>