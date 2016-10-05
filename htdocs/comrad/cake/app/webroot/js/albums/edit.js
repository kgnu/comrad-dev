$(function() {
	var checkCompilation = function() {
		if ($('input[type="checkbox"][name="data[Album][a_Compilation]"]').is(':checked')) {
			$('input[name="data[Album][a_Artist]"]').closest('.control-group').hide();
			$('#tracks td.track-artist, #tracks th.track-artist').show();
		} else {
			$('input[name="data[Album][a_Artist]"]').closest('.control-group').show();
			$('#tracks td.track-artist, #tracks th.track-artist').hide();
		}
	}
	
	$('input[type="checkbox"][name="data[Album][a_Compilation]"]').change(checkCompilation);
	
	checkCompilation();
});
