<?php
class RecurringShow extends AbstractDBObject
{
	public function __construct($params = array())
	{
		$this->columns = array(
			'ID' => array(
				'type' => 'PrimaryKey'
			),
			'TimeInfoID' => array(
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
			'HasHost' => array(
				'type' => 'Boolean',
				'required' => true
			),
			'Title' => array(
				'type' => 'UppercaseString',
				'required' => true,
				'titlecolumn' => true
			),
			'mp3_code' => array(
				'type' => 'ShortString',
				'tostring' => 'MP3 Code',
				'required' => true
			),
			'record_audio' => array(
				'type' => 'Boolean',
				'required' => true,
				'tostring' => 'Record Audio?'
			),
			'ShowURL' => array(
				'type' => 'ShortString',
				'tostring' => 'Show URL'
			),
			'Source' => array(
				'type' => 'Enumeration',
				'possiblevalues' => array(
					'KGNU',
					'Ext'
				),
				'required' => true
			),
			'Category' => array(
				'type' => 'Enumeration',
				'possiblevalues' => array(
					'Announcements',
					'Mix',
					'Music',
					'NewsPA',
					'OurMusic'
				),
				'required' => true
			),
			'Class' => array(
				'type' => 'ShortString',
				'required'=> true
			),
			'DescriptionShort' => array(
				'type' => 'String',
				'tostring' => 'Short Description'
			),
			'DescriptionLong' => array(
				'type' => 'String',
				'tostring' => 'Long Description'
			),
			'Active' => array(
				'type' => 'Boolean',
				'required' => true,
				'default' => true
			)
		);
		
		parent::__construct($params);
	}
	 
	public function getTableName()
	{
		return "RecurringShow";
	}
	 
	public function __toString()
	{
		return $this->Title;
	}
}
?>