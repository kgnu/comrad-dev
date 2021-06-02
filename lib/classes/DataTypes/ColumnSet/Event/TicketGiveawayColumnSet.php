<?php
class TicketGiveawayColumnSet extends EventColumnSet
{
	public function __construct()
	{
		parent::__construct();
		
		$this->addColumns(array(
			'ShowName' => array(
				'type' => 'ShortString',
				'tostring' => 'Show Name'
			),
			'ShowDate' => array(
				'type' => 'Date',
				'tostring' => 'Show Date'
			),
			'Venue' => array(
				'type' => 'ShortString'
			),
			'Copy' => array(
				'defaultvalue' => file_get_contents(
									str_replace('classes/DataTypes/ColumnSet/Event', 'resources/TicketGiveawayDefaultCopy.html', dirname(__FILE__))
								), 
				'type' => 'String',
				'tostring' => 'Show Details',
				'required' => true
			),
			'NotesToDJ' => array(
				'defaultvalue' => file_get_contents(
									str_replace('classes/DataTypes/ColumnSet/Event', 'resources/TicketGiveawayDefaultNotesToDJ.html', dirname(__FILE__))
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
					'Digital Ticket',
					'Other Giveaway'
				)
			),
			'TicketQuantity' => array(
				'type' => 'Integer',
				'tostring' => 'Ticket Quantity',
				'default' => 2
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
			),
			'DeliveryMethod' => array(
				'type' => 'ShortString',
				'tostring' => 'Delivery Method'
			)
		));
	}
}
?>
