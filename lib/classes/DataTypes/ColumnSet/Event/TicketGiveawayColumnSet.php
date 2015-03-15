<?php
class TicketGiveawayColumnSet extends EventWithCopyColumnSet
{
	public function __construct()
	{
		parent::__construct();
		
		$this->addColumns(array(
			'EventDate' => array(
				'type' => 'Date',
				'required' => true,
				'tostring' => 'Event Date'
			),
			'VenueId' => array(
				'type' => 'ForeignKey',
				'foreignType' => 'Venue',
				'required' => true,
				'tostring' => 'Venue'
			),
			'WinnerName' => array(
				'type' => 'UppercaseString',
				'tostring' => 'Winner Name'
			),
			'WinnerPhone' => array(
				'type' => 'ShortString',
				'tostring' => 'Winner Phone'
			),
			'TicketType' => array(
				'type' => 'Enumeration',
				'required' => true,
				'possiblevalues' => array(
					'Hard Ticket',
					'Guest List'
				)
			)
		));
	}
}
?>
