<?php
class PSAColumnSet extends EventWithCopyColumnSet
{
	public function __construct()
	{
		parent::__construct();
		
		$this->addColumns(array(
			'PSACategoryId' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'PSACategory',
				'required' => true,
				'tostring' => 'Category'
			),
			'PSACategory' => array(
				'type' => 'ForeignKeyItem',
				'foreignType' => 'PSACategory',
				'localcolumn' => 'PSACategoryId',
				'foreigncolumn' => 'Id',
				'required' => false,
				'showinform' => false
			),
			'StartDate' => array(
				'type' => 'Date',
				'required' => true,
				'tostring' => 'Start Date'
			),
			'KillDate' => array(
				'type' => 'Date',
				'required' => true,
				'tostring' => 'Kill Date'
			),
			'OrgName' => array(
				'type' => 'UppercaseString',
				'required' => true,
				'tostring' => 'Organization Name'
			),
			'ContactName' => array(
				'type' => 'UppercaseString',
				'required' => true,
				'tostring' => 'Contact Name'
			),
			'ContactPhone' => array(
				'type' => 'ShortString',
				'required' => true,
				'tostring' => 'Contact Phone'
			),
			'ContactWebsite' => array(
				'type' => 'ShortString',
				'tostring' => 'Contact Website'
			),
			'ContactEmail' => array(
				'type' => 'ShortString',
				'tostring' => 'Contact Email'
			)
		));
	}
}
?>
