$(function() {
	$('select[name="data[TimeInfo][ti_DISCRIMINATOR]"]').change(function() {
		console.log($(this).val());
		var val = $(this).val();
		
		$('#time-info .repeat').hide();
		$('#time-info .repeat .weekly').hide();
		
		if (val === 'NonRepeatingTimeInfo') {
			$('#time-info .repeat').hide();
		} else {
			$('#time-info .repeat').show();
		}
		
		if (val === 'DailyRepeatingTimeInfo') {
			$('#time-info .repeat .daily').show();
			$('#time-info .interval-help').html('day(s)');
		} else {
			$('#time-info .repeat .daily').hide();
		}
		
		if (val === 'WeeklyRepeatingTimeInfo') {
			$('#time-info .repeat.weekly').show();
			$('#time-info .interval-help').html('week(s)');
		} else {
			$('#time-info .repeat.weekly').hide();
		}
		// if ($('input[type="checkbox"][name="data[Album][a_Compilation]"]').is(':checked')) {
		// 	$('input[name="data[Album][a_Artist]"]').closest('.control-group').hide();
		// 	$('#tracks td.track-artist, #tracks th.track-artist').show();
		// } else {
		// 	$('input[name="data[Album][a_Artist]"]').closest('.control-group').show();
		// 	$('#tracks td.track-artist, #tracks th.track-artist').hide();
		// }
	});
	
	$('select[name="data[TimeInfo][ti_DISCRIMINATOR]"]').trigger('change');
});
