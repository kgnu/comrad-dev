$(function() {
	var addTrack = function() {
		var $tr, index = 0;
		
		$('#tracks tr').each(function() {
			var inputs = $(this).find('input');
			if (inputs.length > 0) {
				index = Math.max(index, parseInt(inputs.get(0).name.match(/[0-9]+/)[0], 10)) + 1;
			}
		});
		
		$tr = $('#tracks tbody tr:last-child').clone();
		$tr.find('input').val('').attr('name', function() { return $(this).attr('name').replace(/[0-9]+/, index); });
		$tr.find('.close').click(removeTrack);
		$tr.appendTo('#tracks tbody');
		
		checkCloseLinks();
		
		return false;
	}
	
	var removeTrack = function() {
		$(this).closest('tr').remove();
		
		checkCloseLinks();
		
		return false;
	};
	
	var checkCloseLinks = function() {
		if ($('#tracks tbody tr').length > 1) {
			$('#tracks .close').show();
		} else {
			$('#tracks .close').hide();
		}
	}
	
	var checkCompilation = function() {
		if ($('input[type="checkbox"][name="data[Album][a_Compilation]"]').is(':checked')) {
			$('input[name="data[Album][a_Artist]"]').closest('.control-group').hide();
			$('#tracks td.track-artist, #tracks th.track-artist').show();
		} else {
			$('input[name="data[Album][a_Artist]"]').closest('.control-group').show();
			$('#tracks td.track-artist, #tracks th.track-artist').hide();
		}
	}
	
	$('#tracks .close').click(removeTrack);
	$('#tracks a.add-track').click(addTrack);
	$('input[type="checkbox"][name="data[Album][a_Compilation]"]').change(checkCompilation);
	
	checkCompilation();
	checkCloseLinks();
});
