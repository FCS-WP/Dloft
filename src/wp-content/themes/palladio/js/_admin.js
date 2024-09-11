/* global jQuery:false */
/* global PALLADIO_STORAGE:false */

jQuery(document).ready(function() {
	"use strict";

	// jQuery Tabs
	if (jQuery.ui && jQuery.ui.tabs)
		jQuery('.palladio_tabs:not(.inited)').addClass('inited').tabs();
		
	// Init jQuery Accordion
	if (jQuery.ui && jQuery.ui.accordion) {
		jQuery('.palladio_accordion:not(.inited)').addClass('inited').accordion({
			'header': '.palladio_accordion_title',
			'heightStyle': 'content'
		});
	}

	// Hide empty override-options
	jQuery('.postbox > .inside').each(function() {
		if (jQuery(this).html().length < 5) jQuery(this).parent().hide();
	});

	// Hide admin notice
	jQuery('#palladio_admin_notice .palladio_hide_notice').on('click', function(e) {
		jQuery('#palladio_admin_notice').slideUp();
		jQuery.post( PALLADIO_STORAGE['ajax_url'], {'action': 'palladio_hide_admin_notice'}, function(response){});
		e.preventDefault();
		return false;
	});


	// TGMPA Source selector is changed
	jQuery('.tgmpa_source_file').on('change', function(e) {
		var chk = jQuery(this).parents('tr').find('>th>input[type="checkbox"]');
		if (chk.length == 1) {
			if (jQuery(this).val() != '')
				chk.attr('checked', 'checked');
			else
				chk.removeAttr('checked');
		}
	});



	// Add icon selector after the menu item classes field
	jQuery('.edit-menu-item-classes')
		.on('change', function() {
			var icon = palladio_get_icon_class(jQuery(this).val());
			var selector = jQuery(this).next('.palladio_list_icons_selector');
			selector.attr('class', palladio_chg_icon_class(selector.attr('class'), icon));
			if (!icon)
				selector.css('background-image', '');
			else if (icon.indexOf('image-') >= 0) {
				var list = jQuery('.palladio_list_icons');
				if (list.length > 0) {
					var bg = list.find('.'+icon.replace('image-', '')).css('background-image');
					if (bg && bg!='none') selector.css('background-image', bg);
				}
			}
		})
		.each(function() {
			jQuery(this).after('<span class="palladio_list_icons_selector" title="'+PALLADIO_STORAGE['icon_selector_msg']+'"></span>');
			jQuery(this).trigger('change');
		});

	jQuery('.palladio_list_icons_selector').on('click', function(e) {
		var selector = jQuery(this);
		var input_id = selector.prev().attr('id');
		if (input_id === undefined) {
			input_id = ('palladio_icon_field_'+Math.random()).replace(/\./g, '');
			selector.prev().attr('id', input_id)
		}
		var in_menu = selector.parents('.menu-item-settings').length > 0;
		var list = in_menu ? jQuery('.palladio_list_icons') : selector.next('.palladio_list_icons');
		if (list.length > 0) {
			if (list.css('display')=='none') {
				list.find('span.palladio_list_active').removeClass('palladio_list_active');
				var icon = palladio_get_icon_class(selector.attr('class'));
				if (icon != '') list.find('span[class*="'+icon.replace('image-', '')+'"]').addClass('palladio_list_active');
				var pos = in_menu ? selector.offset() : selector.position();
				list.data('input_id', input_id).css({'left': pos.left-(in_menu ? 0 : list.outerWidth()-selector.width()-1), 'top': pos.top+(in_menu ? 0 : selector.height()+4)}).fadeIn();
			} else
				list.fadeOut();
		}
		e.preventDefault();
		return false;
	});

	jQuery('.palladio_list_icons span').on('click', function(e) {
		var list = jQuery(this).parent().fadeOut();
		var input = jQuery('#'+list.data('input_id'));
		var selector = input.next();
		var icon = palladio_alltrim(jQuery(this).attr('class').replace(/palladio_list_active/, ''));
		var bg = jQuery(this).css('background-image');
		if (bg && bg!='none') icon = 'image-'+icon;
		input.val(palladio_chg_icon_class(input.val(), icon)).trigger('change');
		selector.attr('class', palladio_chg_icon_class(selector.attr('class'), icon));
		if (bg && bg!='none') selector.css('background-image', bg);
		e.preventDefault();
		return false;
	});

	function palladio_chg_icon_class(classes, icon) {
		var chg = false;
		classes = palladio_alltrim(classes).split(' ');
		icon = icon.split('-');
		for (var i=0; i<classes.length; i++) {
			if (classes[i].indexOf(icon[0]+'-') >= 0) {
				classes[i] = icon.join('-');
				chg = true;
				break;
			}
		}
		if (!chg) {
			if (classes.length == 1 && classes[0] == '')
				classes[0] = icon.join('-');
			else
				classes.push(icon.join('-'));
		}
		return classes.join(' ');
	}

	function palladio_get_icon_class(classes) {
		var classes = palladio_alltrim(classes).split(' ');
		var icon = '';
		for (var i=0; i<classes.length; i++) {
			if (classes[i].indexOf('icon-') >= 0) {
				icon = classes[i];
				break;
			} else if (classes[i].indexOf('image-') >= 0) {
				icon = classes[i];
				break;
			}
		}
		return icon;
	}
		
	// Init checklist
	jQuery('.palladio_checklist:not(.inited)').addClass('inited')
		.on('change', 'input[type="checkbox"]', function() {
			var choices = '';
			var cont = jQuery(this).parents('.palladio_checklist');
			cont.find('input[type="checkbox"]').each(function() {
				choices += (choices ? '|' : '') + jQuery(this).data('name') + '=' + (jQuery(this).get(0).checked ? jQuery(this).val() : '0');
			});
			cont.siblings('input[type="hidden"]').eq(0).val(choices).trigger('change');
		})
		.each(function() {
			if (jQuery.ui.sortable && jQuery(this).hasClass('palladio_sortable')) {
				var id = jQuery(this).attr('id');
				if (id === undefined)
					jQuery(this).attr('id', 'palladio_sortable_'+(''+Math.random()).replace('.', ''));
				jQuery(this).sortable({
					items: ".palladio_sortable_item",
					placeholder: ' palladio_checklist_item_label palladio_sortable_item palladio_sortable_placeholder',
					update: function(event, ui) {
						var choices = '';
						ui.item.parent().find('input[type="checkbox"]').each(function() {
							choices += (choices ? '|' : '') 
									+ jQuery(this).data('name') + '=' + (jQuery(this).get(0).checked ? jQuery(this).val() : '0');
						});
						ui.item.parent().siblings('input[type="hidden"]').eq(0).val(choices).trigger('change');
					}
				})
				.disableSelection();
			}
		});
		

	// Scheme Editor
	//------------------------------------------------------------------
	
	// Show/Hide colors on change scheme editor type
	jQuery('.palladio_scheme_editor_type input').on('change', function() {
		var type = jQuery(this).val();
		jQuery(this).parents('.palladio_scheme_editor').find('.palladio_scheme_editor_colors .palladio_scheme_editor_row').each(function() {
			var visible = type != 'simple';
			jQuery(this).find('input').each(function() {
				var color_name = jQuery(this).attr('name'),
					fld_visible = type != 'simple';
				if (!fld_visible) {
					for (var i in palladio_simple_schemes) {
						if (i == color_name || typeof palladio_simple_schemes[i][color_name] != 'undefined') {
							fld_visible = true;
							break;
						}
					}
				}
				if (!fld_visible)
					jQuery(this).fadeOut();
				else
					jQuery(this).fadeIn();
				visible = visible || fld_visible;
			});
			if (!visible)
				jQuery(this).slideUp();
			else
				jQuery(this).slideDown();
		});
	});
	jQuery('.palladio_scheme_editor_type input:checked').trigger('change');

	// Change colors on change color scheme
	jQuery('.palladio_scheme_editor_selector').on('change', function(e) {
		var scheme = jQuery(this).val();
		for (var opt in palladio_color_schemes[scheme].colors) {
			var fld = jQuery(this).siblings('.palladio_scheme_editor_colors').find('input[name="'+opt+'"]');
			if (fld.length == 0) continue;
			fld.val( palladio_color_schemes[scheme].colors[opt] );
			palladio_scheme_editor_change_field_colors(fld);
		}
	});

	// Color picker
	palladio_color_picker();
	jQuery('.palladio_scheme_editor_colors .iColorPicker').each(function() {
		palladio_scheme_editor_change_field_colors(jQuery(this));
	}).on('focus', function (e) {
		palladio_color_picker_show(null, jQuery(this), function(fld, clr) {
			fld.val(clr).trigger('change');
			palladio_scheme_editor_change_field_colors(fld);
		});
	}).on('change', function(e) {
		var color_name = jQuery(this).attr('name'),
			color_value = jQuery(this).val();
		// Change value in the color scheme storage
		palladio_color_schemes[jQuery(this).parents('.palladio_scheme_editor').find('.palladio_scheme_editor_selector').val()].colors[color_name] = color_value;
		if (typeof wp.customize != 'undefined')
			wp.customize('scheme_storage').set(palladio_serialize(palladio_color_schemes))
		else
			jQuery(this).parents('form').find('[data-param="scheme_storage"] > input[type="hidden"]').val(palladio_serialize(palladio_color_schemes));
		// Change field colors
		palladio_scheme_editor_change_field_colors(jQuery(this));
		// Change dependent colors
		if (jQuery(this).parents('.palladio_scheme_editor').find('.palladio_scheme_editor_type input:checked').val() == 'simple') {
			if (typeof palladio_simple_schemes[color_name] != 'undefined') {
				var scheme_name = jQuery('.palladio_scheme_editor_selector').val();
				for (var i in palladio_simple_schemes[color_name]) {
					var chg_fld = jQuery(this).parents('.palladio_scheme_editor_colors').find('input[name="'+i+'"]');
					if (chg_fld.length > 0) {
						var level = palladio_simple_schemes[color_name][i];
						// Make color_value darkness
						if (level != 1) {
							var hsb = palladio_hex2hsb(color_value);
							hsb['b'] = Math.min(100, Math.max(0, hsb['b'] * (hsb['b'] < 70 ? 2-level : level)));
							color_value = palladio_hsb2hex(hsb).toLowerCase();
						}
						chg_fld.val(color_value).trigger('change');
					}
				}
			}
		}
	});
	
	// Change color in the field
	function palladio_scheme_editor_change_field_colors(fld) {
		var clr = fld.val(),
			hsb = palladio_hex2hsb(clr);
		fld.css({
			'backgroundColor': clr,
			'color': hsb['b'] < 70 ? '#fff' : '#000'
		});
	}



	// Standard WP Color Picker
	if (jQuery('.palladio_color_selector').length > 0) {
		jQuery('.palladio_color_selector').wpColorPicker({
			// you can declare a default color here,
			// or in the data-default-color attribute on the input
			//defaultColor: false,
	
			// a callback to fire whenever the color changes to a valid color
			change: function(e, ui){
				jQuery(e.target).val(ui.color).trigger('change');
			},
	
			// a callback to fire when the input is emptied or an invalid color
			clear: function(e) {
				jQuery(e.target).prev().trigger('change')
			},
	
			// hide the color picker controls on load
			//hide: true,
	
			// show a group of common colors beneath the square
			// or, supply an array of colors to customize further
			//palettes: true
		});
	}


	// Media selector
	PALLADIO_STORAGE['media_id'] = '';
	PALLADIO_STORAGE['media_frame'] = [];
	PALLADIO_STORAGE['media_link'] = [];
	jQuery('.palladio_media_selector').on('click', function(e) {
		palladio_show_media_manager(this);
		e.preventDefault();
		return false;
	});
	jQuery('.palladio_options_field_preview').on('click', '> span', function(e) {
		var image = jQuery(this);
		var button = image.parent().prev('.palladio_media_selector');
		var field = jQuery('#'+button.data('linked-field'));
		if (field.length == 0) return;
		if (button.data('multiple')==1) {
			var val = field.val().split('|');
			val.splice(image.index(), 1);
			field.val(val.join('|'));
			image.remove();
		} else {
			field.val('');
			image.remove();
		}
		e.preventDefault();
		return false;
	});

	function palladio_show_media_manager(el) {
		PALLADIO_STORAGE['media_id'] = jQuery(el).attr('id');
		PALLADIO_STORAGE['media_link'][PALLADIO_STORAGE['media_id']] = jQuery(el);
		// If the media frame already exists, reopen it.
		if ( PALLADIO_STORAGE['media_frame'][PALLADIO_STORAGE['media_id']] ) {
			PALLADIO_STORAGE['media_frame'][PALLADIO_STORAGE['media_id']].open();
			return false;
		}
		var type = PALLADIO_STORAGE['media_link'][PALLADIO_STORAGE['media_id']].data('type') 
						? PALLADIO_STORAGE['media_link'][PALLADIO_STORAGE['media_id']].data('type') 
						: 'image';
		var args = {
			// Set the title of the modal.
			title: PALLADIO_STORAGE['media_link'][PALLADIO_STORAGE['media_id']].data('choose'),
			// Multiple choise
			multiple: PALLADIO_STORAGE['media_link'][PALLADIO_STORAGE['media_id']].data('multiple')==1 
						? 'add' 
						: false,
			// Customize the submit button.
			button: {
				// Set the text of the button.
				text: PALLADIO_STORAGE['media_link'][PALLADIO_STORAGE['media_id']].data('update'),
				// Tell the button not to close the modal, since we're
				// going to refresh the page when the image is selected.
				close: true
			}
		};
		// Allow sizes and filters for the images
		if (type == 'image') {
			args['frame'] = 'post';
		}
		// Tell the modal to show only selected post types
		if (type == 'image' || type == 'audio' || type == 'video') {
			args['library'] = {
				type: type
			};
		}
		PALLADIO_STORAGE['media_frame'][PALLADIO_STORAGE['media_id']] = wp.media(args);

		// When an image is selected, run a callback.
		PALLADIO_STORAGE['media_frame'][PALLADIO_STORAGE['media_id']].on( 'insert select', function(selection) {
			// Grab the selected attachment.
			var field = jQuery("#"+PALLADIO_STORAGE['media_link'][PALLADIO_STORAGE['media_id']].data('linked-field')).eq(0);
			var attachment = null, attachment_url = '';
			if (PALLADIO_STORAGE['media_link'][PALLADIO_STORAGE['media_id']].data('multiple')===1) {
				PALLADIO_STORAGE['media_frame'][PALLADIO_STORAGE['media_id']].state().get('selection').map( function( att ) {
					attachment_url += (attachment_url ? "|" : "") + att.toJSON().url;
				});
				var val = field.val();
				attachment_url = val + (val ? "|" : '') + attachment_url;
			} else {
				attachment = PALLADIO_STORAGE['media_frame'][PALLADIO_STORAGE['media_id']].state().get('selection').first().toJSON();
				attachment_url = attachment.url;
				var sizes_selector = jQuery('.media-modal-content .attachment-display-settings select.size');
				if (sizes_selector.length > 0) {
					var size = palladio_get_listbox_selected_value(sizes_selector.get(0));
					if (size != '') attachment_url = attachment.sizes[size].url;
				}
			}
			// Display images in the preview area
			var preview = field.siblings('.palladio_options_field_preview');
			if (preview.length == 0) {
				jQuery('<span class="palladio_options_field_preview"></span>').insertAfter(field);
				preview = field.siblings('.palladio_options_field_preview');
			}
			if (preview.length != 0) preview.empty();
			var images = attachment_url.split("|");
			for (var i=0; i<images.length; i++) {
				if (preview.length != 0) {
					var ext = palladio_get_file_ext(images[i]);
					preview.append('<span>'
									+ (ext=='gif' || ext=='jpg' || ext=='jpeg' || ext=='png' 
											? '<img src="'+images[i]+'">'
											: '<a href="'+images[i]+'">'+palladio_get_file_name(images[i])+'</a>'
										)
									+ '</span>');
				}
			}
			// Update field
			field.val(attachment_url).trigger('change');
		});

		// Finally, open the modal.
		PALLADIO_STORAGE['media_frame'][PALLADIO_STORAGE['media_id']].open();
		return false;
	}

});