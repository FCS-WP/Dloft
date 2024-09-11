(function() {
	var count = 0;
	var hreftop = window.location.href;
	var framesrc = jQuery('#mainframe').attr('src');
	
	jQuery(document).ready(function(){

		set_height();

		// Set frame size
		jQuery('.frame_controls li a').click(function(){
			var size = jQuery(this).data('width');
			var className = jQuery(this).data('device');
			var framewidth = size + 17;
			if (className == 'desktop')	framewidth = '100%';
			jQuery(this).parents('ul').find('a').removeClass('current');
			jQuery(this).addClass('current');
			jQuery('#showframe').css({'width': size}).attr('class', className);
			jQuery('#mainframe').css({'width': framewidth});
			return false;
		});
		
		// Switch theme
		jQuery('.themenames li a').click(function(e){
			window.location.href = jQuery(this).attr('href');
			e.preventDefault();
			return false;
		});
		
		// Close frame
		jQuery('#closeframe').click(function(){
			var url = jQuery('#mainframe').attr('src');
			window.location.href = url;
			return false;
		});		
		
		// Open / Close theme selector
		jQuery('.current_theme').click(function(){
			jQuery(this).next().slideToggle();
			return false;
		});
		
		// Show image on theme's name hover
		var placeholder = jQuery('#theme_selector .placeholder img');
		if (placeholder.length > 0) {
			jQuery('.themenames li a').hover(
				function(){
					var img = jQuery(this).data('image');
					placeholder.attr('src', img);
				},
				function(){
					placeholder.attr('src', placeholder.data('image'));
				}
			);
		}

		// QR Code init
		jQuery('#qr_block').empty().qrcode({
			text: jQuery('#mainframe').attr('src')
		});
		jQuery('#show_qr').click(function(){
			jQuery('.qr_wrap').slideToggle(300);
			return false;
		});
		jQuery('.qr_wrap').click(function(){
			jQuery(this).slideUp();
		});
		
	});
	
	jQuery(window).resize(function(){
		set_height();
	});			

	function set_height() {
		var win_height = jQuery(window).height();
		var header_height = jQuery('#header').outerHeight();
		var frame_height = win_height - header_height;
		jQuery('#mainframe').height(frame_height);
	}

})();