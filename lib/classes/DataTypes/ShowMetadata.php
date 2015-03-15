<?php
class ShowMetadata extends AbstractDBObject
{
	public function __construct($params = array())
	{
		$this->columns = array(
			'UID' => array(
				'type' => 'Integer',
				'primarykey' => true
			),
			'Name' => array(
				'type' => 'UppercaseString',
				'required' => true,
				'titlecolumn' => true
			),
			'HostID' => array(
				'type' => 'Integer',
				'tostring' => 'Host'
			),
			'HasHost' => array(
				'type' => 'Boolean',
				'required' => true,
				'tostring' => 'Require Host?',
				'default' => true
			),
			'Duration' => array(
				'type' => 'Integer',
				'required' => true,
				'tostring' => 'Duration (in minutes)',
				'default' => 60
			),
			'StartDateTime' => array(
				'type' => 'Date',
				'required' => true,
				'tostring' => 'Start Date'
			),
			'DescriptionShort' => array(
				'type' => 'String',
				'tostring' => 'Short Description'
			),
			'DescriptionLong' => array(
				'type' => 'String',
				'tostring' => 'Long Description'
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
			'Source' => array('type' => 'Enumeration', 'possiblevalues' => array('KGNU', 'Ext'), 'required' => true),
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
		return "ShowMetadata";
	}
	 
	public function __toString()
	{
		return $this->Name;
	}
}
?>
