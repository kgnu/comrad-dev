<?php
class TicketGiveawayColumnSet extends EventColumnSet
{
	public function __construct()
	{
		parent::__construct();
		
		$this->addColumns(array(
			'Copy' => array(
				'defaultvalue' => file_get_contents(
									str_replace('classes/DataTypes/ColumnSet/Event', 'resources/TicketGiveawayDefaultCopy.html', dirname(__FILE__))
								), 
				'type' => 'String',
				'required' => true
			),
			'TicketType' => array(
				'type' => 'Enumeration',
				'tostring' => 'Ticket Type',
				'required' => true,
				'possiblevalues' => array(
					'Paper Ticket',
					'Guest List Ticket',
					'Other Giveaway'
				)
			),
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
