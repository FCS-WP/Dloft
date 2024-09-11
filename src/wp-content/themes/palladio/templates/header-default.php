<?php
/**
 * The template to display default site header
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */


$palladio_header_css = $palladio_header_image = '';
$palladio_header_video = palladio_get_header_video();
if (true || empty($palladio_header_video)) {
	$palladio_header_image = get_header_image();
	if (palladio_is_on(palladio_get_theme_option('header_image_override')) && apply_filters('palladio_filter_allow_override_header_image', true)) {
		if (is_category()) {
			if (($palladio_cat_img = palladio_get_category_image()) != '')
				$palladio_header_image = $palladio_cat_img;
		} else if (is_singular() || palladio_storage_isset('blog_archive')) {
			if (has_post_thumbnail()) {
				$palladio_header_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
				if (is_array($palladio_header_image)) $palladio_header_image = $palladio_header_image[0];
			} else
				$palladio_header_image = '';
		}
	}
}

?><header class="top_panel top_panel_default<?php
					echo !empty($palladio_header_image) || !empty($palladio_header_video) ? ' with_bg_image' : ' without_bg_image';
					if ($palladio_header_video!='') echo ' with_bg_video';
					if ($palladio_header_image!='') echo ' '.esc_attr(palladio_add_inline_css_class('background-image: url('.esc_url($palladio_header_image).');'));
					if (is_single() && has_post_thumbnail()) echo ' with_featured_image';
					if (palladio_is_on(palladio_get_theme_option('header_fullheight'))) echo ' header_fullheight trx-stretch-height';
					?> scheme_<?php echo esc_attr(palladio_is_inherit(palladio_get_theme_option('header_scheme')) 
													? palladio_get_theme_option('color_scheme') 
													: palladio_get_theme_option('header_scheme'));
					?>"><?php

	// Background video
	if (!empty($palladio_header_video)) {
		get_template_part( 'templates/header-video' );
	}
	
	// Main menu
	if (palladio_get_theme_option("menu_style") == 'top') {
		get_template_part( 'templates/header-navi' );
	}

	// Page title and breadcrumbs area
	get_template_part( 'templates/header-title-default');

	// Header widgets area
	get_template_part( 'templates/header-widgets' );

?></header>