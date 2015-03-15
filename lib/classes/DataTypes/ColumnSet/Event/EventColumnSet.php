<?php
class EventColumnSet extends AbstractColumnSet
{
	public function __construct()
	{
		parent::__construct();
		
		$this->addColumns(array(
			'Id' => array(
				'type' => 'PrimaryKey'
			),
			'Title' => array(
				'type' => 'UppercaseString',
				'required' => true,
				'titlecolumn' => true
			),
			'Active' => array(
				'type' => 'Boolean',
				'required' => true,
				'default' => true
			)
		));
	}
}
?>
