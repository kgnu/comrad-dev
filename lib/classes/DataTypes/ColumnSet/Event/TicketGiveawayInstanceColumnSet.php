<?php
class TicketGiveawayInstanceColumnSet extends EventWithCopyInstanceColumnSet
{
	public function __construct()
	{
		parent::__construct();
		
		$this->addColumns(array(
			'NoCallers' => array(
				'type' => 'Boolean',
				'tostring' => 'No Callers'
			),
			'WinnerName' => array(
				'type' => 'UppercaseString',
				'tostring' => 'Winner\'s Name'
			),
			'WinnerPhone' => array(
				'type' => 'ShortString',
				'tostring' => 'Winner\'s Phone Number'
			),
			'WinnerEmail' => array(
				'type' => 'ShortString',
				'tostring' => 'Winner\'s Email'
			),
			'WinnerAddress' => array(
				'type' => 'ShortString',
				'tostring' => 'Winner\'s Address'
			),
			'IsListenerMember' => array(
				'type' => 'Boolean',
				'tostring' => 'Mark if the winner is a KGNU listener-member'
			)
		));
		
	}
}
?>
