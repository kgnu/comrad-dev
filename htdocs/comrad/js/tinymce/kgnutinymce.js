var kgnuTinyMCEInitialized = false;

function initializeKGNUTinyMCEForSelector(selector) {
	if ($(selector).length > 0) kgnuTinyMCEInitialized = true;
	$(selector).tinymce({
		script_url: 'js/jquery/tinymce/tinymce/tiny_mce.js',
		
		// General options...
		theme: "advanced",
		// file_browser_callback: "tinyBrowser", 
		plugins: "safari,inlinepopups,paste,media",
		mode: "textareas",
		// Theme options (add 'code' button for debugging)...
		theme_advanced_buttons1: "styleselect,|,easylink,easyimage,|,charmap,|,media",
		theme_advanced_buttons2: "",
		theme_advanced_buttons3: "",
		theme_advanced_toolbar_location: "top",
		theme_advanced_toolbar_align: "left",
		theme_advanced_statusbar_location: "bottom",
		theme_advanced_path: false,
		theme_advanced_resizing: true,
		theme_advanced_resize_horizontal: false,
		object_resizing: true,
		width: '100%',
		
		// Disable styles of pasted text
		paste_auto_cleanup_on_paste: true,
		paste_convert_headers_to_strong: false,
		paste_strip_class_attributes: "all",
		paste_remove_spans: true,
		paste_remove_styles: true,
		paste_preprocess : function(pl, o) {
			// Strip out all HTML tags
			o.content = o.content.replace(/<[\/]{0,1}[^><]+>/g, '');
		},
		
		// Setup for styleselect drop down...
		theme_advanced_styles: "CD or Book Title=rte_global_cd_or_book_title",
		content_css: "css/rte_global.css",
		//Prevent users from click Ctrl-B to bold text.
		invalid_elements : "strong,span",
		
		// We don't want urls to be converted or we'll break our formatting on
		//  the pages when they're displayed...
		convert_urls: false,
		
		setup: function(ed) {
			ed.addButton('easylink', {
				title: 'Insert Link',
				image: 'media/icon-link.png', 
				onclick: function() {
					// Get link information from user...
					var linkTitle = $(this).tinymce().selection.getContent({format: 'text'});
		
					// Make sure the something is selected...
					if (linkTitle.trim() == '')
					{
						alert('Please select some text to create a link.');
						return;
					}
		
					// Ask user what their URL is...
					var linkAddress = prompt("To what URL should this link go?\n\n(ex: http://kgnu.org/)", 'http://');
					if (linkAddress.trim() == '') return;
					
					// Determine the protocol
					var split = linkAddress.split('://');
					split[0] = (split.length > 0 ? split[0] : 'http');
					linkAddress = split.join('://');
		
					// Determine link anchor...
					var html = '<a href="' + linkAddress + '"';
					if (linkAddress.toLowerCase().indexOf('kgnu.org') == -1) {
						html += ' target="_blank">';
					} else {
						html += '>';
					}
					html += linkTitle + '</a>';
		
					// Insert link...
					$(this).tinymce().execCommand('mceReplaceContent', false, html);
				}
			});
			
			ed.addButton('easyimage', {
				title: 'Insert Image',
				image: 'media/icon-image.png', 
				onclick: function() {
		
					// Get the link to the image...
					var imgAddress = prompt('What is the URL of the image to insert?\n\n(ex: http://www.kgnu.org/logo.gif)');
					if (imgAddress.trim() == '') return;
		
					// Compile the html and insert it...
					var imgHtml = '<img src="' + imgAddress + '" />';
					$(this).tinymce().execCommand('mceInsertContent', false, imgHtml);
				}
			});
		}
	});
}