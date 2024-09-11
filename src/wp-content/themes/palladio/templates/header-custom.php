<?php
/**
 * The template to display custom header from the ThemeREX Addons Layouts
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0.06
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
$palladio_header_id = str_replace('header-custom-', '', palladio_get_theme_option("header_style"));
if ((int) $palladio_header_id == 0) {
	$palladio_header_id = palladio_get_post_id(array(
												'name' => $palladio_header_id,
												'post_type' => defined('TRX_ADDONS_CPT_LAYOUT_PT') ? TRX_ADDONS_CPT_LAYOUT_PT : 'cpt_layouts'
												)
											);
	} else {
    $palladio_header_id = apply_filters('trx_addons_filter_get_translated_layout', $palladio_header_id);
}
$palladio_header_meta = get_post_meta($palladio_header_id, 'trx_addons_options', true);

?><header class="top_panel top_panel_custom top_panel_custom_<?php echo esc_attr($palladio_header_id); 
				?> top_panel_custom_<?php echo esc_attr(sanitize_title(get_the_title($palladio_header_id)));
				echo !empty($palladio_header_image) || !empty($palladio_header_video) 
					? ' with_bg_image' 
					: ' without_bg_image';
				if ($palladio_header_video!='') 
					echo ' with_bg_video';
				if ($palladio_header_image!='') 
					echo ' '.esc_attr(palladio_add_inline_css_class('background-image: url('.esc_url($palladio_header_image).');'));
				if (!empty($palladio_header_meta['margin']) != '') 
					echo ' '.esc_attr(palladio_add_inline_css_class('margin-bottom: '.esc_attr(palladio_prepare_css_value($palladio_header_meta['margin'])).';'));
				if (is_single() && has_post_thumbnail()) 
					echo ' with_featured_image';
				if (palladio_is_on(palladio_get_theme_option('header_fullheight'))) 
					echo ' header_fullheight trx-stretch-height';
				?> scheme_<?php echo esc_attr(palladio_is_inherit(palladio_get_theme_option('header_scheme')) 
												? palladio_get_theme_option('color_scheme') 
												: palladio_get_theme_option('header_scheme'));
				?>"><?php

	// Background video
	if (!empty($palladio_header_video)) {
		get_template_part( 'templates/header-video' );
	}
		
	// Custom header's layout
	do_action('palladio_action_show_layout', $palladio_header_id);

	// Header widgets area
	get_template_part( 'templates/header-widgets' );
		
?></header>