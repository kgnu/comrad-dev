$(function() {
	$('#calendar').fullCalendar({
		height: 475,
		defaultView: 'agendaWeek',
		allDaySlot: false,
		header: false,
		viewDisplay: function(view) {
			$('#title').html(view.title);
		},
		events: function(start, end, callback) {
			var seiLoaded = $.ajax({
				url: 'api/scheduled_event_instances.json',
				dataType: 'json',
				data: {
					'limit': 0,
					'fields': JSON.stringify([ 'sei_Id', 'sei_DISCRIMINATOR', 'sei_StartDateTime', 'sei_Duration', 'sei_ScheduledEventId' ]),
					'contain': JSON.stringify({
						'ScheduledEvent': {
							'fields': [ 'se_Id', 'se_EventId'],
							'Event': {
								'fields': [ 'e_DISCRIMINATOR', 'e_Title' ]
							}
						}
					}),
					'conditions': JSON.stringify({
						'sei_DISCRIMINATOR': 'ScheduledShowInstance',
						'sei_StartDateTime >=': $.fullCalendar.formatDate(start, 'yyyy-MM-dd HH:mm:ss'),
						'sei_StartDateTime <': $.fullCalendar.formatDate(end, 'yyyy-MM-dd HH:mm:ss')
					})
				}
			});
			
			$.when(seiLoaded).then(function(seiResponse) {
				var scheduledEventInstances = seiResponse;
				
				console.log(scheduledEventInstances);
				
				var startDateTime, events = [];
				for (var i = 0; i < scheduledEventInstances.response.length; i++) {
					startDateTime = new Date(scheduledEventInstances.response[i].ScheduledEventInstance.sei_StartDateTime);
					events.push({
						allDay: false,
						title: scheduledEventInstances.response[i].ScheduledEvent.Event.e_Title,
						start: startDateTime,
						end: new Date(startDateTime.getTime() + scheduledEventInstances.response[i].ScheduledEventInstance.sei_Duration * 60 * 1000),
					});
				}
				callback(events);
			});
		}
	});
	
	$('#previous').click(function() {
		$('#calendar').fullCalendar('prev');
		return false;
	});
	
	$('#next').click(function() {
		$('#calendar').fullCalendar('next');
		return false;
	});
});
