/* global jQuery:false */
/* global PALLADIO_STORAGE:false */

jQuery(document).ready(function() {
	"use strict";

	var theme_init_counter = 0;
	var vc_resize = false;
	
	palladio_init_actions();

    // Theme init actions
	function palladio_init_actions() {

		if (PALLADIO_STORAGE['vc_edit_mode'] && jQuery('.vc_empty-placeholder').length==0 && theme_init_counter++ < 30) {
			setTimeout(palladio_init_actions, 200);
			return;
		}
	
		// Add resize on VC action vc-full-width-row
		// But we emulate 'action.resize_vc_row_start' and 'action.resize_vc_row_end'
		// to correct resize sliders and video inside 'boxed' pages
		jQuery(document).on('action.resize_vc_row_start', function(e, el) {
			vc_resize = true;
			palladio_resize_actions();
		});
		
		// Check fullheight elements
		jQuery(document).on('action.init_hidden_elements', palladio_stretch_height);
		jQuery(document).on('action.init_shortcodes', palladio_stretch_height);
	
		// Resize handlers
		jQuery(window).resize(function() {
			if (!vc_resize) palladio_resize_actions();
            palladio_fix_open_menu();
            palladio_align_promo_content();
        });
		
		// Scroll handlers
		jQuery(window).scroll(function() {
			palladio_scroll_actions();
        });

        // Scroll handlers with delay
        jQuery(window).scroll(function(){
            setTimeout( function(){
                palladio_fix_open_menu();
            }, 1);
        });
		
		// First call to init core actions
		palladio_ready_actions();

		// Wait for VC init
		setTimeout(function() {
			if (!vc_resize) palladio_resize_actions();
			palladio_scroll_actions();
		}, 1);

		// Custom functions
        palladio_fix_open_menu();
        palladio_align_promo_content();
        palladio_add_number_anchor_sidemenu();
	}
	
	
	
	// Theme first load actions
	//==============================================
	function palladio_ready_actions() {
	
		// Add scheme class and js support
		//------------------------------------
		document.documentElement.className = document.documentElement.className.replace(/\bno-js\b/,'js');
		if (document.documentElement.className.indexOf(PALLADIO_STORAGE['site_scheme'])==-1)
			document.documentElement.className += ' ' + PALLADIO_STORAGE['site_scheme'];

		// Init background video
		//------------------------------------
		// Use Bideo to play local video
		if (PALLADIO_STORAGE['background_video'] && jQuery('.top_panel.with_bg_video').length > 0 && window.Bideo) {
			// Waiting 10ms after mejs init
			setTimeout(function() {
				jQuery('.top_panel.with_bg_video').prepend('<video id="background_video" loop muted></video>');
				var bv = new Bideo();
				bv.init({
					// Video element
					videoEl: document.querySelector('#background_video'),
					
					// Container element
					container: document.querySelector('.top_panel'),
					
					// Resize
					resize: true,
					
					// autoplay: false,
					
					isMobile: window.matchMedia('(max-width: 768px)').matches,
					
					playButton: document.querySelector('#background_video_play'),
					pauseButton: document.querySelector('#background_video_pause'),
					
					// Array of objects containing the src and type
					// of different video formats to add
					// For example:
					//	src: [
					//			{	src: 'night.mp4', type: 'video/mp4' }
					//			{	src: 'night.webm', type: 'video/webm;codecs="vp8, vorbis"' }
					//		]
					src: [
						{
							src: PALLADIO_STORAGE['background_video'],
							type: 'video/'+palladio_get_file_ext(PALLADIO_STORAGE['background_video'])
						}
					],
					
					// What to do once video loads (initial frame)
					onLoad: function () {
						//document.querySelector('#background_video_cover').style.display = 'none';
					}
				});
			}, 10);
		
		// Use Tubular to play video from Youtube
		} else if (jQuery.fn.tubular) {
			jQuery('div#background_video').each(function() {
				var youtube_code = jQuery(this).data('youtube-code');
				if (youtube_code) {
					jQuery(this).tubular({videoId: youtube_code});
					jQuery('#tubular-player').appendTo(jQuery(this)).show();
					jQuery('#tubular-container,#tubular-shield').remove();
				}
			});
		}
	
		// Tabs
		//------------------------------------
		if (jQuery('.palladio_tabs:not(.inited)').length > 0 && jQuery.ui && jQuery.ui.tabs) {
			jQuery('.palladio_tabs:not(.inited)').each(function () {
				// Get initially opened tab
				var init = jQuery(this).data('active');
				if (isNaN(init)) {
					init = 0;
					var active = jQuery(this).find('> ul > li[data-active="true"]').eq(0);
					if (active.length > 0) {
						init = active.index();
						if (isNaN(init) || init < 0) init = 0;
					}
				} else {
					init = Math.max(0, init);
				}
				// Init tabs
				jQuery(this).addClass('inited').tabs({
					active: init,
					show: {
						effect: 'fadeIn',
						duration: 300
					},
					hide: {
						effect: 'fadeOut',
						duration: 300
					},
					create: function( event, ui ) {
						if (ui.panel.length > 0) jQuery(document).trigger('action.init_hidden_elements', [ui.panel]);
					},
					activate: function( event, ui ) {
						if (ui.newPanel.length > 0) jQuery(document).trigger('action.init_hidden_elements', [ui.newPanel]);
					}
				});
			});
		}
		// AJAX loader for the tabs
		jQuery('.palladio_tabs_ajax').on( "tabsbeforeactivate", function( event, ui ) {
			if (ui.newPanel.data('need-content')) palladio_tabs_ajax_content_loader(ui.newPanel, 1, ui.oldPanel);
		});
		// AJAX loader for the pages in the tabs
		jQuery('.palladio_tabs_ajax').on( "click", '.nav-links a', function(e) {
			var panel = jQuery(this).parents('.palladio_tabs_content');
			var page = 1;
			var href = jQuery(this).attr('href');
			var pos = -1;
			if ((pos = href.lastIndexOf('/page/')) != -1 ) {
				page = Number(href.substr(pos+6).replace("/", ""));
				if (!isNaN(page)) page = Math.max(1, page);
			}
			palladio_tabs_ajax_content_loader(panel, page);
			e.preventDefault();
			return false;
		});
	
		
		// Headers
		//----------------------------------------------
	
		var rows = jQuery('.palladio_layouts_row_fixed');
		
		// If page contain fixed rows
		if (rows.length > 0) {
			
			// Add placeholders before each row
			rows.each(function() {
				if (!jQuery(this).next().hasClass('palladio_layouts_row_fixed_placeholder'))
					jQuery(this).after('<div class="palladio_layouts_row_fixed_placeholder"></div>');
			});
	
			jQuery(document).on('action.scroll_palladio', function() {
				palladio_fix_header(rows, false);
			});
			jQuery(document).on('action.resize_palladio', function() {
				palladio_fix_header(rows, true);
			});
		}
	
		// Menu
		//----------------------------------------------
	
		// Add TOC in the side menu
		if (jQuery('.menu_side_inner').length > 0 && jQuery('#toc_menu').length > 0)
			jQuery('#toc_menu').appendTo('.menu_side_inner');
	
		// Open/Close side menu
		jQuery('.menu_side_button').on('click', function(e){
			jQuery(this).parent().toggleClass('opened');
			e.preventDefault();
			return false;
		});

		// Add images to the menu items with classes image-xxx
		jQuery('.sc_layouts_menu li[class*="image-"]').each(function() {
			var classes = jQuery(this).attr('class').split(' ');
			var icon = '';
			for (var i=0; i<classes.length; i++) {
				if (classes[i].indexOf('image-') >= 0) {
					icon = classes[i].replace('image-', '');
					break;
				}
			}
			if (icon) jQuery(this).find('>a').css('background-image', 'url('+PALLADIO_STORAGE['theme_url']+'/trx_addons/css/icons.png/'+icon+'.png');
		});
	
		// Add arrows to the mobile menu
		jQuery('.menu_mobile .menu-item-has-children > a').append('<span class="open_child_menu"></span>');
	
		// Open/Close mobile menu
		jQuery('.sc_layouts_menu_mobile_button > a,.menu_mobile_button,.menu_mobile_description').on('click', function(e) {
			if (jQuery(this).parent().hasClass('sc_layouts_menu_mobile_button_burger') && jQuery(this).next().hasClass('sc_layouts_menu_popup')) return;
			if (jQuery(this).hasClass('close')) {
                jQuery('.menu_mobile_overlay').fadeOut();
                jQuery('.menu_mobile').removeClass('opened');
                jQuery(this).removeClass('close');
                jQuery(document).trigger('action.start_wheel_handlers');
                e.preventDefault();
                return false;
            } else {
                jQuery('.menu_mobile_overlay').fadeIn();
                jQuery('.menu_mobile').addClass('opened');
                jQuery(this).addClass('close');
                jQuery(document).trigger('action.stop_wheel_handlers');
                e.preventDefault();
                return false;
            }
		});
		jQuery(document).on('keypress', function(e) {
			if (e.keyCode == 27) {
				if (jQuery('.menu_mobile.opened').length == 1) {
					jQuery('.menu_mobile_overlay').fadeOut();
					jQuery('.menu_mobile').removeClass('opened');
					jQuery(document).trigger('action.start_wheel_handlers');
					e.preventDefault();
					return false;
				}
			}
		});
		jQuery('.menu_mobile_close, .menu_mobile_overlay').on('click', function(e){
			jQuery('.menu_mobile_overlay').fadeOut();
			jQuery('.menu_mobile').removeClass('opened');
			jQuery('.sc_layouts_menu_mobile_button > a').removeClass('close');
			jQuery(document).trigger('action.start_wheel_handlers');
			e.preventDefault();
			return false;
		});
	
		// Open/Close mobile submenu
		jQuery('.menu_mobile').on('click', 'li a, li a .open_child_menu', function(e) {
			var $a = jQuery(this).hasClass('open_child_menu') ? jQuery(this).parent() : jQuery(this);
			if ($a.parent().hasClass('menu-item-has-children')) {
				if ($a.attr('href')=='#' || jQuery(this).hasClass('open_child_menu')) {
					if ($a.siblings('ul:visible').length > 0)
						$a.siblings('ul').slideUp().parent().removeClass('opened');
					else {
						jQuery(this).parents('li').siblings('li').find('ul:visible').slideUp().parent().removeClass('opened');
						$a.siblings('ul').slideDown().parent().addClass('opened');
					}
				}
			}
			if (!jQuery(this).hasClass('open_child_menu') && palladio_is_local_link($a.attr('href')))
				jQuery('.menu_mobile_close').trigger('click');
			if (jQuery(this).hasClass('open_child_menu') || $a.attr('href')=='#') {
				e.preventDefault();
				return false;
			}
		});
	
		if (!PALLADIO_STORAGE['trx_addons_exist'] || jQuery('.top_panel.top_panel_default .sc_layouts_menu_default').length > 0) {
			// Init superfish menus
			palladio_init_sfmenu('.sc_layouts_menu:not(.inited) > ul:not(.inited)');
			// Show menu		
			jQuery('.sc_layouts_menu:not(.inited)').each(function() {
				if (jQuery(this).find('>ul.inited').length == 1) jQuery(this).addClass('inited');
			});
			// Generate 'scroll' event after the menu is showed
			jQuery(window).trigger('scroll');
		}

		
		// Forms
		//----------------------------------------------
	
		// Comment form
		jQuery("form#commentform").submit(function(e) {
			var rez = palladio_comments_validate(jQuery(this));
			if (!rez)
				e.preventDefault();
			return rez;
		});
	
		jQuery("form").on('keypress', '.error_field', function() {
			if (jQuery(this).val() != '')
				jQuery(this).removeClass('error_field');
		});
	
	
		// Blocks with stretch width
		//----------------------------------------------
		// Action to prepare stretch blocks in the third-party plugins
		jQuery(document).trigger('action.prepare_stretch_width');
		// Wrap stretch blocks
		jQuery('.trx-stretch-width').wrap('<div class="trx-stretch-width-wrap"></div>');
		jQuery('.trx-stretch-width').after('<div class="trx-stretch-width-original"></div>');
		palladio_stretch_width();
			
	
		// Pagination
		//------------------------------------
	
		// Load more
		jQuery('.nav-links-more a').on('click', function(e) {
			if (PALLADIO_STORAGE['load_more_link_busy']) return;
			PALLADIO_STORAGE['load_more_link_busy'] = true;
			var more = jQuery(this);
			var page = Number(more.data('page'));
			var max_page = Number(more.data('max-page'));
			if (page >= max_page) {
				more.parent().hide();
				return;
			}
			more.parent().addClass('loading');
			var panel = more.parents('.palladio_tabs_content');
			if (panel.length == 0) {															// Load simple page content
				jQuery.get(location.href, {
					paged: page+1
				}).done(function(response) {
					// Get inline styles and add to the page styles
					var selector = 'palladio-inline-styles-inline-css';
					var p1 = response.indexOf(selector);
					if (p1 < 0) {
						selector = 'trx_addons-inline-styles-inline-css';
						p1 = response.indexOf(selector);
					}
					if (p1 > 0) {
						p1 = response.indexOf('>', p1) + 1;
						var p2 = response.indexOf('</style>', p1);
						var inline_css_add = response.substring(p1, p2);
						var inline_css = jQuery('#'+selector);
						if (inline_css.length == 0)
							jQuery('body').append('<style id="'+selector+'" type="text/css">' + inline_css_add + '</style>');
						else
							inline_css.append(inline_css_add);
					}
					// Get new posts and append to the .posts_container
					palladio_loadmore_add_items(jQuery('.content .posts_container').eq(0),
											   jQuery(response).find('.content .posts_container > article,'
											   						+'.content .posts_container > div[class*="column-"],'
																	+'.content .posts_container > .masonry_item')
												);
				});
			} else {																			// Load tab's panel content
				jQuery.post(PALLADIO_STORAGE['ajax_url'], {
					nonce: PALLADIO_STORAGE['ajax_nonce'],
					action: 'palladio_ajax_get_posts',
					blog_template: panel.data('blog-template'),
					blog_style: panel.data('blog-style'),
					posts_per_page: panel.data('posts-per-page'),
					cat: panel.data('cat'),
					parent_cat: panel.data('parent-cat'),
					post_type: panel.data('post-type'),
					taxonomy: panel.data('taxonomy'),
					page: page+1
				}).done(function(response) {
					var rez = {};
					try {
						rez = JSON.parse(response);
					} catch (e) {
						rez = { error: PALLADIO_STORAGE['strings']['ajax_error'] };
						console.log(response);
					}
					if (rez.error !== '') {
						panel.html('<div class="palladio_error">'+rez.error+'</div>');
					} else {
						palladio_loadmore_add_items(panel.find('.posts_container'), jQuery(rez.data).find('article'));
					}
				});
			}
			// Append items to the container
			function palladio_loadmore_add_items(container, items) {
				if (container.length > 0 && items.length > 0) {
					container.append(items);
					if (container.hasClass('portfolio_wrap') || container.hasClass('masonry_wrap')) {
						container.masonry( 'appended', items ).masonry();
						if (container.hasClass('gallery_wrap')) {
							PALLADIO_STORAGE['GalleryFx'][container.attr('id')].appendItems();
						}
					}
					more.data('page', page+1).parent().removeClass('loading');
					// Remove TOC if exists (rebuild on init_shortcodes)
					jQuery('#toc_menu').remove();
					// Trigger actions to init new elements
					PALLADIO_STORAGE['init_all_mediaelements'] = true;
					jQuery(document).trigger('action.init_shortcodes', [container.parent()]);
					jQuery(document).trigger('action.init_hidden_elements', [container.parent()]);
				}
				if (page+1 >= max_page)
					more.parent().hide();
				else
					PALLADIO_STORAGE['load_more_link_busy'] = false;
				// Fire 'window.scroll' after clearing busy state
				jQuery(window).trigger('scroll');
				// Fire 'window.resize'
				jQuery( window ).trigger( 'resize' );
			}
			e.preventDefault();
			return false;
		});
	
		// Infinite scroll
		jQuery(document).on('action.scroll_palladio', function(e) {
			if (PALLADIO_STORAGE['load_more_link_busy']) return;
			var container = jQuery('.content > .posts_container').eq(0);
			var inf = jQuery('.nav-links-infinite');
			if (inf.length == 0) return;
			if (container.offset().top + container.height() < jQuery(window).scrollTop() + jQuery(window).height()*1.5)
				inf.find('a').trigger('click');
		});

        // Comments
        //------------------------------------

        // Checkbox with "I agree..."
        if (jQuery('input[type="checkbox"][name="i_agree_privacy_policy"]:not(.inited),input[type="checkbox"][name="gdpr_terms"]:not(.inited),input[type="checkbox"][name="wpgdprc"]:not(.inited)').length > 0) {
            jQuery('input[type="checkbox"][name="i_agree_privacy_policy"]:not(.inited),input[type="checkbox"][name="gdpr_terms"]:not(.inited),input[type="checkbox"][name="wpgdprc"]:not(.inited)')
                .addClass('inited')
                .on('change', function(e) {
                    if (jQuery(this).get(0).checked)
                        jQuery(this).parents('form').find('button,input[type="submit"]').removeAttr('disabled');
                    else
                        jQuery(this).parents('form').find('button,input[type="submit"]').attr('disabled', 'disabled');
                }).trigger('change');
        }



        // Other settings
		//------------------------------------
	
		jQuery(document).trigger('action.ready_palladio');
	
		// Init post format specific scripts
		jQuery(document).on('action.init_hidden_elements', palladio_init_post_formats);
	
		// Init hidden elements (if exists)
		jQuery(document).trigger('action.init_hidden_elements', [jQuery('body').eq(0)]);
		
	} //end ready
	
	
	
	
	// Scroll actions
	//==============================================
	
	// Do actions when page scrolled
	function palladio_scroll_actions() {

		var scroll_offset = jQuery(window).scrollTop();
		var adminbar_height = Math.max(0, jQuery('#wpadminbar').height());
	
		// Call theme/plugins specific action (if exists)
		//----------------------------------------------
		jQuery(document).trigger('action.scroll_palladio');
		
		// Fix/unfix sidebar
		palladio_fix_sidebar();

		// Shift top and footer panels when header position equal to 'Under content'
		if (jQuery('body').hasClass('header_position_under') && !palladio_browser_is_mobile()) {
			var delta = 50;
			var adminbar = jQuery('#wpadminbar');
			var adminbar_height = adminbar.length == 0 && adminbar.css('position') == 'fixed' ? 0 : adminbar.height();
			var header = jQuery('.top_panel');
			var header_height = header.height();
			var mask = header.find('.top_panel_mask');
			if (mask.length==0) {
				header.append('<div class="top_panel_mask"></div>');
				mask = header.find('.top_panel_mask');
			}
			if (scroll_offset > adminbar_height) {
				var offset = scroll_offset - adminbar_height;
				if (offset <= header_height) {
					var mask_opacity = Math.max(0, Math.min(0.8, (offset-delta)/header_height));
					// Don't shift header with Revolution slider in Chrome
					if ( !(/Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor)) || header.find('.slider_engine_revo').length == 0 )
						header.css('top', Math.round(offset/1.2)+'px');
					mask.css({
						'opacity': mask_opacity,
						'display': offset==0 ? 'none' : 'block'
					});
				} else if (parseInt(header.css('top')) != 0) {
					header.css('top', Math.round(offset/1.2)+'px');
				}
			} else if (parseInt(header.css('top')) != 0 || mask.css('display')!='none') {
				header.css('top', '0px');
				mask.css({
					'opacity': 0,
					'display': 'none'
				});
			}
			var footer = jQuery('.footer_wrap');
			var footer_height = Math.min(footer.height(), jQuery(window).height());
			var footer_visible = (scroll_offset + jQuery(window).height()) - (header.outerHeight() + jQuery('.page_content_wrap').outerHeight());
			if (footer_visible > 0) {
				mask = footer.find('.top_panel_mask');
				if (mask.length==0) {
					footer.append('<div class="top_panel_mask"></div>');
					mask = footer.find('.top_panel_mask');
				}
				if (footer_visible <= footer_height) {
					var mask_opacity = Math.max(0, Math.min(0.8, (footer_height - footer_visible)/footer_height));
					// Don't shift header with Revolution slider in Chrome
					if ( !(/Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor)) || footer.find('.slider_engine_revo').length == 0 )
						footer.css('top', -Math.round((footer_height - footer_visible)/1.2)+'px');
					mask.css({
						'opacity': mask_opacity,
						'display': footer_height - footer_visible <= 0 ? 'none' : 'block'
					});
				} else if (parseInt(footer.css('top')) != 0 || mask.css('display')!='none') {
					footer.css('top', 0);
					mask.css({
						'opacity': 0,
						'display': 'none'
					});
				}
			}
		}
	}
	
	
	// Resize actions
	//==============================================
	
	// Do actions when page scrolled
	function palladio_resize_actions(cont) {
		palladio_check_layout();
		palladio_fix_sidebar();
		palladio_fix_footer();
		palladio_stretch_width(cont);
		palladio_stretch_height(null, cont);
		palladio_stretch_bg_video();
                palladio_resize_video( cont );
		palladio_vc_row_fullwidth_to_boxed(cont);
		if (PALLADIO_STORAGE['menu_side_stretch']) palladio_stretch_sidemenu();
	
		// Call theme/plugins specific action (if exists)
		//----------------------------------------------
		jQuery(document).trigger('action.resize_palladio', [cont]);
	}
	
	// Stretch sidemenu (if present)
	function palladio_stretch_sidemenu() {
		var toc_items = jQuery('.menu_side_wrap .toc_menu_item');
		if (toc_items.length < 5) return;
		var toc_items_height = jQuery(window).height() - jQuery('.menu_side_wrap .sc_layouts_logo').outerHeight() - toc_items.length;
		var th = Math.floor(toc_items_height / toc_items.length);
		var th_add = toc_items_height - th*toc_items.length;
		toc_items.find(".toc_menu_description,.toc_menu_icon").css({
			'height': th+'px',
			'lineHeight': th+'px'
		});
		toc_items.eq(0).find(".toc_menu_description,.toc_menu_icon").css({
			'height': (th+th_add)+'px',
			'lineHeight': (th+th_add)+'px'
		});
	}
	
	// Check for mobile layout
	function palladio_check_layout() {
		if (jQuery('body').hasClass('no_layout'))
			jQuery('body').removeClass('no_layout');
		var w = window.innerWidth;
		if (w == undefined) 
			w = jQuery(window).width()+(jQuery(window).height() < jQuery(document).height() || jQuery(window).scrollTop() > 0 ? 16 : 0);
		if (PALLADIO_STORAGE['mobile_layout_width'] >= w) {
			if (!jQuery('body').hasClass('mobile_layout')) {
				jQuery('body').removeClass('desktop_layout').addClass('mobile_layout');
			}
		} else {
			if (!jQuery('body').hasClass('desktop_layout')) {
				jQuery('body').removeClass('mobile_layout').addClass('desktop_layout');
				jQuery('.menu_mobile').removeClass('opened');
				jQuery('.menu_mobile_overlay').hide();
			}
		}
		if (PALLADIO_STORAGE['mobile_device'] || palladio_browser_is_mobile()) 
			jQuery('body').addClass('mobile_device');
	}
	
	// Stretch area to full window width
	function palladio_stretch_width(cont) {
		if (cont===undefined) cont = jQuery('body');
		cont.find('.trx-stretch-width').each(function() {
			var $el = jQuery(this);
			var $el_cont = $el.parents('.page_wrap');
			var $el_cont_offset = 0;
			if ($el_cont.length == 0) 
				$el_cont = jQuery(window);
			else
				$el_cont_offset = $el_cont.offset().left;
			var $el_full = $el.next('.trx-stretch-width-original');
			var el_margin_left = parseInt( $el.css( 'margin-left' ), 10 );
			var el_margin_right = parseInt( $el.css( 'margin-right' ), 10 );
			var offset = $el_cont_offset - $el_full.offset().left - el_margin_left;
			var width = $el_cont.width();
			if (!$el.hasClass('inited')) {
				$el.addClass('inited invisible');
				$el.css({
					'position': 'relative',
					'box-sizing': 'border-box'
				});
			}
			$el.css({
				'left': offset,
				'width': $el_cont.width()
			});
			if ( !$el.hasClass('trx-stretch-content') ) {
				var padding = Math.max(0, -1*offset);
				var paddingRight = Math.max(0, width - padding - $el_full.width() + el_margin_left + el_margin_right);
				$el.css( { 'padding-left': padding + 'px', 'padding-right': paddingRight + 'px' } );
			}
			$el.removeClass('invisible');
		});
	}
	
	// Stretch area to the full window height
	function palladio_stretch_height(e, cont) {
		if (cont===undefined) cont = jQuery('body');
		cont.find('.trx-stretch-height').each(function () {
			var fullheight_item = jQuery(this);
			// If item now invisible
			if (jQuery(this).parents('div:hidden,section:hidden,article:hidden').length > 0) {
				return;
			}
			var wh = 0;
			var fullheight_row = jQuery(this).parents('.vc_row-o-full-height');
			if (fullheight_row.length > 0) {
				wh = fullheight_row.height();
			} else {
				if (screen.width > 1000) {
					var adminbar = jQuery('#wpadminbar');
					wh = jQuery(window).height() - (adminbar.length > 0 ? adminbar.height() : 0);
				} else
					wh = 'auto';
			}
			if (wh=='auto' || wh > 0) fullheight_item.height(wh);
		});
	}
	
	// Stretch background video
	function palladio_stretch_bg_video() {
		var video_wrap = jQuery('div#background_video');
		if (video_wrap.length == 0) return;
		var video = video_wrap.find('>iframe,>video'),
			w = video_wrap.width(),
			h = video_wrap.height();
		if (w/h < 16/9)
			w = h/9*16;
		else
			h = w/16*9;
		video
			.attr({'width': w, 'height': h})
			.css({'width': w, 'height': h});
	}
		
	// Recalculate width of the vc_row[data-vc-full-width="true"] when content boxed or menu_style=='left|right'
	function palladio_vc_row_fullwidth_to_boxed(row) {
		if (jQuery('body').hasClass('body_style_boxed') || jQuery('body').hasClass('menu_style_side')) {
            if (row === undefined || !row.hasClass('.vc_row') || !row.data('vc-full-width'))
                row = jQuery('.vc_row[data-vc-full-width="true"]');
			var width_content = jQuery('.page_wrap').width();
			var width_content_wrap = jQuery('.page_content_wrap .content_wrap').width();
			var indent = ( width_content - width_content_wrap ) / 2;
			var rtl = jQuery('html').attr('dir') == 'rtl';
			row.each( function() {
				var mrg = parseInt(jQuery(this).css('marginLeft'));
				var stretch_content = jQuery(this).attr('data-vc-stretch-content');
				var in_content = jQuery(this).parents('.content_wrap').length > 0;
				jQuery(this).css({
					'width': width_content,
					'left': rtl ? 'auto' : (in_content ? -indent : 0) - mrg,
					'right': !rtl ? 'auto' : (in_content ? -indent : 0) - mrg,
					'padding-left': stretch_content ? 0 : indent + mrg,
					'padding-right': stretch_content ? 0 : indent + mrg
				});
			});
		}
	}
	
	
	// Fix/unfix header
	function palladio_fix_header(rows, resize) {
		if (jQuery(window).width() <= 800) {
			rows.removeClass('palladio_layouts_row_fixed_on').css({'top': 'auto'});
			return;
		}
		var scroll_offset = jQuery(window).scrollTop();
		var admin_bar = jQuery('#wpadminbar');
		var rows_offset = Math.max(0, admin_bar.length > 0 && admin_bar.css('position')=='fixed' ? admin_bar.height() : 0);
		
		rows.each(function() {
			var placeholder = jQuery(this).next();
			var offset = parseInt(jQuery(this).hasClass('palladio_layouts_row_fixed_on') ? placeholder.offset().top : jQuery(this).offset().top);
			if (isNaN(offset)) offset = 0;
	
			// Fix/unfix row
			if (scroll_offset + rows_offset <= offset) {
				if (jQuery(this).hasClass('palladio_layouts_row_fixed_on')) {
					jQuery(this).removeClass('palladio_layouts_row_fixed_on').css({'top': 'auto'});
				}
			} else {
				var h = jQuery(this).outerHeight();
				if (!jQuery(this).hasClass('palladio_layouts_row_fixed_on')) {
					if (rows_offset + h < jQuery(window).height() * 0.33) {
						placeholder.height(h);
						jQuery(this).addClass('palladio_layouts_row_fixed_on').css({'top': rows_offset+'px'});
						h = jQuery(this).outerHeight();
					}
				} else if (resize && jQuery(this).hasClass('palladio_layouts_row_fixed_on') && jQuery(this).offset().top != rows_offset) {
					jQuery(this).css({'top': rows_offset+'px'});
				}
				rows_offset += h;
			}
		});
	}
	
	
	// Fix/unfix footer
	function palladio_fix_footer() {
		if (jQuery('body').hasClass('header_position_under') && !palladio_browser_is_mobile()) {
			var ft = jQuery('.footer_wrap');
			if (ft.length > 0) {
				var ft_height = ft.outerHeight(false),
					pc = jQuery('.page_content_wrap'),
					pc_offset = pc.offset().top,
					pc_height = pc.height();
				if (pc_offset + pc_height + ft_height < jQuery(window).height()) {
					if (ft.css('position')!='absolute') {
						ft.css({
							'position': 'absolute',
							'left': 0,
							'bottom': 0,
							'width' :'100%'
						});
					}
				} else {
					if (ft.css('position')!='relative') {
						ft.css({
							'position': 'relative',
							'left': 'auto',
							'bottom': 'auto'
						});
					}
				}
			}
		}
	}
	
	
	// Fix/unfix sidebar
	function palladio_fix_sidebar() {
		var sb = jQuery('.sidebar');
		var content = sb.siblings('.content');
		if (sb.length > 0) {
	
			// Unfix when sidebar is under content
			if (content.css('float') == 'none') {
				if (sb.css('position')!='static') {
					sb.css({
						'float': sb.hasClass('right') ? 'right' : 'left',
						'position': 'static'
					});
				}
	
			} else {
	
				var sb_height = sb.outerHeight();
				var content_height = content.outerHeight();
				var content_top = content.offset().top;
				var scroll_offset = jQuery(window).scrollTop();
				var top_panel_fixed_height = jQuery('.top_panel').length > 0 ? jQuery('.top_panel').outerHeight() : 0;
				if (jQuery('.sc_layouts_row_fixed_on').length > 0) {
					top_panel_fixed_height = 0;
					jQuery('.sc_layouts_row_fixed_on').each(function() {
						top_panel_fixed_height += jQuery(this).outerHeight();
					});
				}
	
				if (sb_height < content_height && 
					( (sb_height >= jQuery(window).height() && scroll_offset + jQuery(window).height() >= sb_height+30+content_top)
					  ||
					  (sb_height < jQuery(window).height() && scroll_offset >= content_top)
					)
				) {
					
					var sb_init = {
							'float': 'none',
							'position': 'fixed',
							'top': 'auto',
							'bottom' : 'auto'
							};
					
					// Fix when sidebar bottom appear
					if (sb.css('position')!=='fixed') {
						if (sb_height + 30 >= jQuery(window).height() - top_panel_fixed_height)
							sb_init.bottom = 30;
						else
							sb_init.top = top_panel_fixed_height;
					}
					
					// Detect horizontal position when resize
					var pos = jQuery('.page_content_wrap .content_wrap').position();
					pos = pos.left + Math.max(0, parseInt(jQuery('.page_content_wrap .content_wrap').css('paddingLeft'))) 
									+ Math.max(0, parseInt(jQuery('.page_content_wrap .content_wrap').css('marginLeft')));
					if (sb.hasClass('right'))
						sb_init.right = pos;
					else
						sb_init.left = pos;
					
					// Shift to top when footer appear
					var footer_top = 0;
					var footer_pos = jQuery('.footer_wrap').position();
					var widgets_below_page_pos = jQuery('.widgets_below_page_wrap').position();
					if (widgets_below_page_pos)
						footer_top = widgets_below_page_pos.top;
					else if (footer_pos)
						footer_top = footer_pos.top;
					if (footer_top > 0 && scroll_offset + jQuery(window).height() >= footer_top + 30) {
						sb_init.position = 'absolute';
						if (sb.hasClass('right'))
							sb_init.right = 0;
						else
							sb_init.left = 0;
						sb_init.bottom = 'auto';
						sb_init.top = (jQuery('.page_content_wrap .content_wrap').height() - sb_height) + 'px';
					}
					
					// Set position
					if (sb.css('position')!=sb_init.position)
						sb.css(sb_init);
	
				} else {
	
					// Unfix when page scrolling to top
					if (sb.css('position')!='static') {
						sb.css({
							'float': sb.hasClass('right') ? 'right' : 'left',
							'position': 'static',
							'top': 'auto',
							'left': 'auto',
							'right': 'auto'
						});
					}
	
				}
			}
		}
	}
	
	
	
	
	
	// Navigation
	//==============================================
	
	// Init Superfish menu
	function palladio_init_sfmenu(selector) {
		jQuery(selector).show().each(function() {
			var animation_in = jQuery(this).parent().data('animation_in');
			if (animation_in == undefined) animation_in = "none";
			var animation_out = jQuery(this).parent().data('animation_out');
			if (animation_out == undefined) animation_out = "none";
			jQuery(this).addClass('inited').superfish({
				delay: 500,
				animation: {
					opacity: 'show'
				},
				animationOut: {
					opacity: 'hide'
				},
				speed: 		animation_in!='none' ? 500 : 200,
				speedOut:	animation_out!='none' ? 500 : 200,
				autoArrows: false,
				dropShadows: false,
				onBeforeShow: function(ul) {
					if (jQuery(this).parents("ul").length > 1){
						var w = jQuery('.page_wrap').width();  
						var par_offset = jQuery(this).parents("ul").offset().left;
						var par_width  = jQuery(this).parents("ul").outerWidth();
						var ul_width   = jQuery(this).outerWidth();
						if (par_offset+par_width+ul_width > w-20 && par_offset-ul_width > 0)
							jQuery(this).addClass('submenu_left');
						else
							jQuery(this).removeClass('submenu_left');
					}
					if (animation_in!='none') {
						jQuery(this).removeClass('animated fast '+animation_out);
						jQuery(this).addClass('animated fast '+animation_in);
					}
				},
				onBeforeHide: function(ul) {
					if (animation_out!='none') {
						jQuery(this).removeClass('animated fast '+animation_in);
						jQuery(this).addClass('animated fast '+animation_out);
					}
				}
			});
		});
	}
	
	
	
	
	// Post formats init
	//=====================================================
	
	function palladio_init_post_formats(e, cont) {

		// Wrap select with .select_container
		palladio_add_select_container( cont );
	
		// MediaElement init
		palladio_init_media_elements(cont);
		
		// Video play button
		cont.find('.format-video .post_featured.with_thumb .post_video_hover:not(.inited)')
			.addClass('inited')
			.on('click', function(e) {
				jQuery(this).parents('.post_featured')
					.addClass('post_video_play')
					.find('.post_video').html(jQuery(this).data('video'));
				jQuery(window).trigger('resize');
				e.preventDefault();
				return false;
			});
	}
	
	
	// Init media elements
	//--------------------------------------------
	PALLADIO_STORAGE['mejs_attempts'] = 0;
	function palladio_init_media_elements(cont) {
		var audio_selector = 'audio:not(.inited)',
			video_selector = 'video:not(.inited):not([nocontrols]):not([controls="0"]):not([controls="false"]):not([controls="no"])',	//:not([autoplay])
			media_selector = audio_selector + ( audio_selector && video_selector ? ',' : '') + video_selector;
		if (PALLADIO_STORAGE['use_mediaelements'] && cont.find( media_selector ).length > 0) {
			if ( window.mejs ) {
				if (window.mejs.MepDefaults) {
					window.mejs.MepDefaults.enableAutosize = true;
				}
				if (window.mejs.MediaElementDefaults) {
					window.mejs.MediaElementDefaults.enableAutosize = true;
				}
				// Disable init for video[autoplay]
				cont.find(
						  // Old shortcode 'wp-video'
						  'video.wp-video-shortcode[autoplay],'
						+ 'video.wp-video-shortcode[nocontrols],'
						+ 'video.wp-video-shortcode[controls="0"],'
						+ 'video.wp-video-shortcode[controls="false"],'
						+ 'video.wp-video-shortcode[controls="no"],'
						// New block 'video'
						+ '.wp-block-video > video[autoplay],'
						+ '.wp-block-video > video[nocontrols],'
						+ '.wp-block-video > video[controls="0"],'
						+ '.wp-block-video > video[controls="false"],'
						+ '.wp-block-video > video[controls="no"]'
						)
					.removeClass('wp-video-shortcode');
				// Init mediaelements
				cont.find( media_selector ).each(
					function() {
						var $self = jQuery( this );
						// If item now invisible
						if ($self.parents( 'div:hidden,section:hidden,article:hidden' ).length > 0) {
							return;
						}
						if (   ! $self.hasClass( 'no-mejs' )
							&& ! $self.hasClass( 'no-mediaelement' )
							&& ! $self.hasClass( 'wp-block-cover__video-background' )
							&& $self.addClass( 'inited' ).parents( '.mejs-mediaelement' ).length === 0
							&& $self.parents( '.wp-block-video' ).length === 0
							&& $self.parents( '.elementor-background-video-container' ).length === 0
							&& $self.parents( '.elementor-widget-video' ).length === 0
							// Prevent init media elements on the video with autoplay
							// Case 1: Don't init media elements on video with autoplay is inside a slider
							// && ( $self.parents( '.with_video_autoplay' ).length === 0 || $self.parents( '.slider-slide' ).length === 0 )
							// Case 2: Don't init media elements on video with autoplay anyway
							&& $self.parents( '.with_video_autoplay' ).length === 0
							// Comment a next row if you want to init mediaelements on the background video inside a layouts title
							&& $self.parents( '.sc_layouts_title' ).length === 0
							// Uncomment the next row to 
							// disable mediaelements init on the Elementor's video shortcode
							// to support a video ratio, specified in a shortcode parameters
							// && $self.parents( '.elementor-fit-aspect-ratio' ).length === 0
							&& ( PALLADIO_STORAGE['init_all_mediaelements']
								|| ( ! $self.hasClass( 'wp-audio-shortcode' )
									&& ! $self.hasClass( 'wp-video-shortcode' )
									&& ! $self.parent().hasClass( 'wp-playlist' )
									)
								)
						) {
							var media_cont = $self.parents('.post_video').eq(0);
							if ( media_cont.length === 0 ) {
								media_cont = $self.parents('.video_frame').eq(0);
								if ( media_cont.length === 0 ) {
									media_cont = $self.parent();
								}
							}
							var //video_w = $self.width(),
								//video_h = $self.height(),
								cont_w = media_cont.length > 0 ? media_cont.width() : -1,
								// If video is inside a container, get the container's height
								//cont_h = media_cont.length > 0 ? Math.floor( cont_w / video_w * video_h ) : -1,
								cont_h = media_cont.length > 0 ? media_cont.height() : -1,
								settings = {
									enableAutosize: true,
									videoWidth:     cont_w,   // if set, overrides <video width>
									videoHeight:    cont_h,   // if set, overrides <video height>
									audioWidth:     '100%',   // width of audio player
									audioHeight:    40,	      // height of audio player
									success: function(mejs) {
										if ( mejs.pluginType && 'flash' === mejs.pluginType && mejs.attributes ) {
											mejs.attributes.autoplay
												&& 'false' !== mejs.attributes.autoplay
												&& mejs.addEventListener( 'canplay', function () { mejs.play(); }, false );
											mejs.attributes.loop
												&& 'false' !== mejs.attributes.loop
												&& mejs.addEventListener( 'ended', function () { mejs.play(); }, false );
										}
									}
								};
							$self.mediaelementplayer( settings );
						}
					}
				);
			} else if ( PALLADIO_STORAGE['mejs_attempts']++ < 5 ) {
				setTimeout( function() { palladio_init_media_elements( cont ); }, 400 );
			}
		}
		// Init all media elements after first run
		setTimeout( function() { PALLADIO_STORAGE['init_all_mediaelements'] = true; }, 1000 );
	}

	// Resize video frames
	function palladio_resize_video(cont) {
		if (cont === undefined) {
			cont = jQuery( 'body' );
		}
		// Resize tags 'video'
		cont.find( 'video' ).each( function() {
			var $self = jQuery( this );
			// If item now invisible
			if ( $self.parents('.mejs-mediaelement').length === 0
				|| $self.hasClass( 'trx_addons_resize' )
				|| $self.hasClass( 'trx_addons_noresize' )
				|| $self.parents( 'div:hidden,section:hidden,article:hidden' ).length > 0
			) {
				return;
			}
			var video     = $self.addClass( 'palladio_resize' ).eq( 0 );
			var ratio     = (video.data( 'ratio' ) !== undefined ? video.data( 'ratio' ).split( ':' ) : [16,9]);
			ratio         = ratio.length != 2 || ratio[0] === 0 || ratio[1] === 0 ? 16 / 9 : ratio[0] / ratio[1];
			var mejs_cont = video.parents( '.mejs-video' ).eq(0);
			var mfp_cont  = video.parents( '.mfp-content' ).eq(0);
			var w_attr    = video.data( 'width' );
			var h_attr    = video.data( 'height' );
			if ( ! w_attr || ! h_attr) {
				w_attr = video.attr( 'width' );
				h_attr = video.attr( 'height' );
				if ( ! w_attr || ! h_attr) {
					return;
				}
				video.data( {'width': w_attr, 'height': h_attr} );
			}
			var percent = ('' + w_attr).substr( -1 ) == '%';
			w_attr      = parseInt( w_attr, 10 );
			h_attr      = parseInt( h_attr, 10 );
			var w_real  = Math.ceil(
							mejs_cont.length > 0
									? Math.min( percent ? 10000 : w_attr, mejs_cont.parents( 'div,article' ).eq(0).width() )
									: Math.min( percent ? 10000 : w_attr, video.parents( 'div,article' ).eq(0).width() )
			);
			if ( mfp_cont.length > 0 ) {
				w_real  = Math.max( mfp_cont.width(), w_real );
			}
			var h_real  = Math.ceil( percent ? w_real / ratio : w_real / w_attr * h_attr );
			if ( parseInt( video.attr( 'data-last-width' ), 10 ) == w_real ) {
				return;
			}
			if ( percent ) {
				video.height( h_real );
			} else if ( video.parents( '.wp-video-playlist' ).length > 0 ) {
				if ( mejs_cont.length === 0 ) {
					video.attr( {'width': w_real, 'height': h_real} );
				}
			} else {
				video.attr( {'width': w_real, 'height': h_real} ).css( {'width': w_real + 'px', 'height': h_real + 'px'} );
				if ( mejs_cont.length > 0 ) {
					palladio_set_mejs_player_dimensions( video, w_real, h_real );
				}
			}
			video.attr( 'data-last-width', w_real );
		} );

		// Resize tags 'iframe'
			cont.find( '.video_frame iframe,iframe' ).each( function() {
				var $self = jQuery( this );
				// If item now invisible
				if ( $self.hasClass( 'trx_addons_resize' ) || $self.hasClass( 'trx_addons_noresize' ) || $self.addClass( 'palladio_resize' ).parents( 'div:hidden,section:hidden,article:hidden' ).length > 0 ) {
					return;
				}
				var iframe = $self.eq( 0 );
				if (iframe.length === 0 || iframe.attr( 'src' ) === undefined || iframe.attr( 'src' ).indexOf( 'soundcloud' ) > 0) {
					return;
				}
				var w_attr = iframe.attr( 'width' );
				var h_attr = iframe.attr( 'height' );
				if ( ! w_attr || ! h_attr || w_attr <= 325 ) {
					return;
				}
				var ratio  = iframe.data( 'ratio' ) !== undefined
								? iframe.data( 'ratio' ).split( ':' )
								: ( iframe.parent().data( 'ratio' ) !== undefined
									? iframe.parent().data( 'ratio' ).split( ':' )
									: ( iframe.find( '[data-ratio]' ).length > 0
										? iframe.find( '[data-ratio]' ).data( 'ratio' ).split( ':' )
										: [w_attr, h_attr]
										)
									);
				ratio      = ratio.length != 2 || ratio[0] === 0 || ratio[1] === 0 ? 16 / 9 : ratio[0] / ratio[1];
				var percent   = ( '' + w_attr ).slice( -1 ) == '%';
				w_attr        = parseInt( w_attr, 10 );
				h_attr        = parseInt( h_attr, 10 );
				var par       = iframe.parents( 'div,section' ).eq(0),
					contains  = iframe.data('contains-in-parent')=='1' || iframe.hasClass('contains-in-parent'),
					nostretch = iframe.data('no-stretch-to-parent')=='1' || iframe.hasClass('no-stretch-to-parent'),
					pw        = Math.ceil( par.width() ),
					ph        = Math.ceil( par.height() ),
					w_real    = nostretch ? Math.min( w_attr, pw ) : pw,
					h_real    = Math.ceil( percent ? w_real / ratio : w_real / w_attr * h_attr );
				if ( contains && par.css( 'position' ) == 'absolute' && h_real > ph) {
					h_real = ph;
					w_real = Math.ceil( percent ? h_real * ratio : h_real * w_attr / h_attr );
				}
				if (parseInt( iframe.attr( 'data-last-width' ), 10 ) == w_real) {
					return;
				}
				iframe.css( {'width': w_real + 'px', 'height': h_real + 'px'} );
				iframe.attr( 'data-last-width', w_real );
			} );
	}  // palladio_resize_video

	// Set Media Elements player dimensions
	function palladio_set_mejs_player_dimensions(video, w, h) {
		if (mejs) {
			for (var pl in mejs.players) {
				if (mejs.players[pl].media.src == video.attr( 'src' )) {
					if (mejs.players[pl].media.setVideoSize) {
						mejs.players[pl].media.setVideoSize( w, h );
					} else if (mejs.players[pl].media.setSize) {
						mejs.players[pl].media.setSize( w, h );
					}
					mejs.players[pl].setPlayerSize( w, h );
					mejs.players[pl].setControlsSize();
				}
		}
	}
	}

	
	
	// Load the tab's content
	function palladio_tabs_ajax_content_loader(panel, page, oldPanel) {
		if (panel.html().replace(/\s/g, '')=='') {
			var height = oldPanel === undefined ? panel.height() : oldPanel.height();
			if (isNaN(height) || height < 100) height = 100;
			panel.html('<div class="palladio_tab_holder" style="min-height:'+height+'px;"></div>');
		} else
			panel.find('> *').addClass('palladio_tab_content_remove');
		panel.data('need-content', false).addClass('palladio_loading');
		jQuery.post(PALLADIO_STORAGE['ajax_url'], {
			nonce: PALLADIO_STORAGE['ajax_nonce'],
			action: 'palladio_ajax_get_posts',
			blog_template: panel.data('blog-template'),
			blog_style: panel.data('blog-style'),
			posts_per_page: panel.data('posts-per-page'),
			cat: panel.data('cat'),
			parent_cat: panel.data('parent-cat'),
			post_type: panel.data('post-type'),
			taxonomy: panel.data('taxonomy'),
			page: page
		}).done(function(response) {
			panel.removeClass('palladio_loading');
			var rez = {};
			try {
				rez = JSON.parse(response);
			} catch (e) {
				rez = { error: PALLADIO_STORAGE['strings']['ajax_error'] };
				console.log(response);
			}
			if (rez.error !== '') {
				panel.html('<div class="palladio_error">'+rez.error+'</div>');
			} else {
				panel.prepend(rez.data).fadeIn(function() {
					jQuery(document).trigger('action.init_shortcodes', [panel]);
					jQuery(document).trigger('action.init_hidden_elements', [panel]);
					jQuery(window).trigger('scroll');
					setTimeout(function() {
						panel.find('.palladio_tab_holder,.palladio_tab_content_remove').remove();
						jQuery(window).trigger('scroll');
					}, 600);
				});
			}
		});
	}
	
	
	// Forms validation
	//-------------------------------------------------------
	
	// Comments form
	function palladio_comments_validate(form) {
		form.find('input').removeClass('error_field');
		var comments_args = {
			error_message_text: PALLADIO_STORAGE['strings']['error_global'],	// Global error message text (if don't write in checked field)
			error_message_show: true,									// Display or not error message
			error_message_time: 4000,									// Error message display time
			error_message_class: 'palladio_messagebox palladio_messagebox_style_error',	// Class appended to error message block
			error_fields_class: 'error_field',							// Class appended to error fields
			exit_after_first_error: false,								// Cancel validation and exit after first error
			rules: [
				{
					field: 'comment',
					min_length: { value: 1, message: PALLADIO_STORAGE['strings']['text_empty'] },
					max_length: { value: PALLADIO_STORAGE['comment_maxlength'], message: PALLADIO_STORAGE['strings']['text_long']}
				}
			]
		};
		if (form.find('.comments_author input[aria-required="true"]').length > 0) {
			comments_args.rules.push(
				{
					field: 'author',
					min_length: { value: 1, message: PALLADIO_STORAGE['strings']['name_empty']},
					max_length: { value: 60, message: PALLADIO_STORAGE['strings']['name_long']}
				}
			);
		}
		if (form.find('.comments_email input[aria-required="true"]').length > 0) {
			comments_args.rules.push(
				{
					field: 'email',
					min_length: { value: 1, message: PALLADIO_STORAGE['strings']['email_empty']},
					max_length: { value: 60, message: PALLADIO_STORAGE['strings']['email_long']},
					mask: { value: PALLADIO_STORAGE['email_mask'], message: PALLADIO_STORAGE['strings']['email_not_valid']}
				}
			);
		}
		var error = palladio_form_validate(form, comments_args);
		return !error;
	}

	/*----- Contact Form 7 -----*/

	jQuery('.wpcf7-form .sc_form_field_submit input[type="submit"]').wrapAll('<div class="sc_form_field sc_form_field_button inited">');

	/*----- Mailchimp for WordPress -----*/

	jQuery('.mc4wp-form input[name="AGREE_TO_TERMS"]').parent().addClass('inited').append('<span></span>');
	
	// Wrap select with .select_container
	function palladio_add_select_container( cont ) {
		if ( jQuery( 'body' ).hasClass( 'wp-admin' ) ) {
			return;
		}
		var selector = 'select:not(.esg-sorting-select):not([class*="trx_addons_attrib_"])';	//:not([size])
		if ( selector ) {
			cont.find( selector ).each( function() {
				var $self = jQuery( this );
				if ( $self.css( 'display' ) != 'none'
					&& $self.parents( '.select_container' ).length === 0
					&& ! $self.next().hasClass( 'select2' )
					&& ! $self.hasClass( 'select2-hidden-accessible' )
					&& ! $self.hasClass( 'components-select-control__input' )
				) {
					var add_class = '';
					if ( $self.prop( 'size' ) > 1 ) {
						add_class += ' select_container_multirows';
					}
					if ( $self.prop( 'multiple' ) ) {
						add_class += ' select_container_multiple';
					}
					$self.wrap( '<div class="select_container' + add_class + '"></div>' );
					// Bubble submit() up for widget "Categories"
					if ( $self.parents( '.widget_categories' ).length > 0 ) {
						$self.parent().get(0).submit = function() {
							jQuery(this).closest('form').eq(0).submit();
						};
					}
				}
			} );
		}
	}
	// Add select_container on 'ajaxComplete'
	jQuery(document).on( 'ajaxComplete', function(e) {
		// Trigger event after timeout to allow other scripts add elements on the page
		setTimeout( function() {
			palladio_add_select_container( jQuery( 'body' ) );
		}, 100 );
	} );

});

function palladio_fix_open_menu() {
	"use strict";
    var top_pos = 0;
    var body_width = jQuery('body').width();
    var control_width = 1135;
    var sc_layouts_row_fixed = jQuery('.sc_layouts_row_fixed');
    var sc_layouts_row_fixed_height = sc_layouts_row_fixed.outerHeight();
    var sc_layouts_row_fixed_outerheight = sc_layouts_row_fixed.outerHeight();
    var adminbar_height = Math.max(0, jQuery('#wpadminbar').height());

    if (sc_layouts_row_fixed.hasClass('sc_layouts_row_fixed_on'))
        top_pos = sc_layouts_row_fixed_height + adminbar_height;
    else
        top_pos = sc_layouts_row_fixed_outerheight + adminbar_height;

    jQuery('.menu_mobile').each(function () {
        if (body_width > control_width) {
            jQuery(this).css({
                top: top_pos,
                height: top_pos !== 0 ? 'calc(100.5% - ' + top_pos + 'px)' : '100.5%'
            });
            jQuery('.menu_mobile').find('.menu_mobile_close').addClass('menu_mobile_close_hidden');
            jQuery('.menu_mobile').find('.sc_layouts_logo').addClass('sc_layouts_logo_hidden');
        } else {
            jQuery(this).css({
                top: adminbar_height,
                height: adminbar_height !== 0 ? 'calc(100.5% - ' + adminbar_height + 'px)' : '100.5%'
            });
            jQuery('.menu_mobile').find('.menu_mobile_close').removeClass('menu_mobile_close_hidden');
            jQuery('.menu_mobile').find('.sc_layouts_logo').removeClass('sc_layouts_logo_hidden');
        }
    });
}

function palladio_align_promo_content() {

    // Promo - align text to the content
    var body_style = jQuery('body');
    var page_wrap = jQuery('.page_wrap').width();
    var width_content = jQuery('.content_wrap').width();
    var indent = (page_wrap - width_content) / 2;
    if (body_style.hasClass('body_style_wide') || body_style.hasClass('body_style_boxed')) {
        if (PALLADIO_STORAGE['mobile_layout_width'] >= window.innerWidth) {
            jQuery('.vc_row[data-vc-stretch-content="true"] .sc_promo.align_text.sc_promo_image_position_left .sc_promo_text').each(function () {
                jQuery(this).css('padding-right', 0);
            });
            jQuery('.vc_row[data-vc-stretch-content="true"] .sc_promo.align_text.sc_promo_image_position_right .sc_promo_text').each(function () {
                jQuery(this).css('padding-left', 0);
            });
        } else {
            jQuery('.vc_row[data-vc-stretch-content="true"] .sc_promo.align_text.sc_promo_image_position_left .sc_promo_text').each(function () {
                jQuery(this).css('padding-right', indent);
            });
            jQuery('.vc_row[data-vc-stretch-content="true"] .sc_promo.align_text.sc_promo_image_position_right .sc_promo_text').each(function () {
                jQuery(this).css('padding-left', indent);
            });
        }
    }
}

function palladio_add_number_anchor_sidemenu() {
	"use strict";
    if (jQuery('body').hasClass('menu_style_default')) {
        jQuery('.toc_menu_icon').each(function ( index ) {
            index++;
            if (index < 10) index = '0' + index;
            jQuery(this).append('<span class="toc_menu_icon_line"></span><span class="toc_menu_icon_number">'+index+'</span>');
        });
    }
}

//Open new windows in new tab
jQuery('a').filter(function() {
    "use strict";
    return this.hostname && this.hostname !== location.hostname;
}).attr('target','_blank');
