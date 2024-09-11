<?php
/* WPBakery Page Builder support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('palladio_vc_theme_setup9')) {
	add_action( 'after_setup_theme', 'palladio_vc_theme_setup9', 9 );
	function palladio_vc_theme_setup9() {
		if (palladio_exists_visual_composer()) {
			add_action( 'wp_enqueue_scripts', 								'palladio_vc_frontend_scripts', 1100 );
			add_filter( 'palladio_filter_merge_styles',						'palladio_vc_merge_styles' );
	
			// Add/Remove params in the standard VC shortcodes
			//-----------------------------------------------------
			add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,					'palladio_vc_add_params_classes', 10, 3 );
			
			// Color scheme
			$scheme = array(
				"param_name" => "scheme",
				"heading" => esc_html__("Color scheme", 'palladio'),
				"description" => wp_kses_data( __("Select color scheme to decorate this block", 'palladio') ),
				"group" => esc_html__('Colors', 'palladio'),
				"admin_label" => true,
				"value" => array_flip(palladio_get_list_schemes(true)),
				"type" => "dropdown"
			);
			vc_add_param("vc_section", $scheme);
			vc_add_param("vc_row", $scheme);
			vc_add_param("vc_row_inner", $scheme);
			vc_add_param("vc_column", $scheme);
			vc_add_param("vc_column_inner", $scheme);
			vc_add_param("vc_column_text", $scheme);

            // Row bg
            vc_add_param("vc_row", array(
                "param_name" => "row_bg",
                "heading" => esc_html__("Additional background", 'palladio'),
                "description" => wp_kses_data( __("Select additional background to decorate this block", 'palladio') ),
                "group" => esc_html__('Colors', 'palladio'),
                "admin_label" => true,
                "value" => array(esc_html__("Add additional background", 'palladio') => "1" ),
                "type" => "checkbox"
            ));


            // Alter height and hide on mobile for Empty Space
			vc_add_param("vc_empty_space", array(
				"param_name" => "alter_height",
				"heading" => esc_html__("Alter height", 'palladio'),
				"description" => wp_kses_data( __("Select alternative height instead value from the field above", 'palladio') ),
				"admin_label" => true,
				"value" => array(
					esc_html__('Tiny', 'palladio') => 'tiny',
					esc_html__('Small', 'palladio') => 'small',
					esc_html__('Medium', 'palladio') => 'medium',
					esc_html__('Large', 'palladio') => 'large',
					esc_html__('Huge', 'palladio') => 'huge',
					esc_html__('From the value above', 'palladio') => 'none'
				),
				"type" => "dropdown"
			));
			vc_add_param("vc_empty_space", array(
				"param_name" => "hide_on_mobile",
				"heading" => esc_html__("Hide on mobile", 'palladio'),
				"description" => wp_kses_data( __("Hide this block on the mobile devices, when the columns are arranged one under another", 'palladio') ),
				"admin_label" => true,
				"std" => 0,
				"value" => array(
					esc_html__("Hide on mobile", 'palladio') => "1",
					esc_html__("Hide on tablet", 'palladio') => "3",
					esc_html__("Hide on notebook", 'palladio') => "2" 
					),
				"type" => "checkbox"
			));
			
			// Add Narrow style to the Progress bars
			vc_add_param("vc_progress_bar", array(
				"param_name" => "narrow",
				"heading" => esc_html__("Narrow", 'palladio'),
				"description" => wp_kses_data( __("Use narrow style for the progress bar", 'palladio') ),
				"std" => 0,
				"value" => array(esc_html__("Narrow style", 'palladio') => "1" ),
				"type" => "checkbox"
			));
			
			// Add param 'Closeable' to the Message Box
			vc_add_param("vc_message", array(
				"param_name" => "closeable",
				"heading" => esc_html__("Closeable", 'palladio'),
				"description" => wp_kses_data( __("Add 'Close' button to the message box", 'palladio') ),
				"std" => 0,
				"value" => array(esc_html__("Closeable", 'palladio') => "1" ),
				"type" => "checkbox"
			));
		}
		if (is_admin()) {
			add_filter( 'palladio_filter_tgmpa_required_plugins',		'palladio_vc_tgmpa_required_plugins' );
			add_filter( 'vc_iconpicker-type-fontawesome',				'palladio_vc_iconpicker_type_fontawesome' );
		}
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'palladio_vc_tgmpa_required_plugins' ) ) {
	
	function palladio_vc_tgmpa_required_plugins($list=array()) {
		if (palladio_storage_isset('required_plugins', 'js_composer')) {
			$path = palladio_get_file_dir('plugins/js_composer/js_composer.zip');
			$list[] = array(
					'name' 		=> palladio_storage_get_array('required_plugins', 'js_composer'),
					'slug' 		=> 'js_composer',
					'version'	=> '7.0',
					'source'	=> !empty($path) ? $path : 'upload://js_composer.zip',
					'required' 	=> false
			);
		}
		return $list;
	}
}

// Check if WPBakery Page Builder installed and activated
if ( !function_exists( 'palladio_exists_visual_composer' ) ) {
	function palladio_exists_visual_composer() {
		return class_exists('Vc_Manager');
	}
}

// Check if WPBakery Page Builder in frontend editor mode
if ( !function_exists( 'palladio_vc_is_frontend' ) ) {
	function palladio_vc_is_frontend() {
		return (isset($_GET['vc_editable']) && $_GET['vc_editable']=='true')
			|| (isset($_GET['vc_action']) && $_GET['vc_action']=='vc_inline');
	}
}
	
// Enqueue VC custom styles
if ( !function_exists( 'palladio_vc_frontend_scripts' ) ) {
	
	function palladio_vc_frontend_scripts() {
		if (palladio_exists_visual_composer()) {
			if (palladio_is_on(palladio_get_theme_option('debug_mode')) && palladio_get_file_dir('plugins/js_composer/js_composer.css')!='')
				wp_enqueue_style( 'palladio-js-composer',  palladio_get_file_url('plugins/js_composer/js_composer.css'), array(), null );
		}
	}
}
	
// Merge custom styles
if ( !function_exists( 'palladio_vc_merge_styles' ) ) {
	
	function palladio_vc_merge_styles($list) {
		$list[] = 'plugins/js_composer/js_composer.css';
		return $list;
	}
}
	
// Add theme icons into VC iconpicker list
if ( !function_exists( 'palladio_vc_iconpicker_type_fontawesome' ) ) {
	
	function palladio_vc_iconpicker_type_fontawesome($icons) {
		$list = palladio_get_list_icons();
		if (!is_array($list) || count($list) == 0) return $icons;
		$rez = array();
		foreach ($list as $icon)
			$rez[] = array($icon => str_replace('icon-', '', $icon));
		return array_merge( $icons, array(esc_html__('Theme Icons', 'palladio') => $rez) );
	}
}



// Shortcodes support
//------------------------------------------------------------------------

// Add params to the standard VC shortcodes
if ( !function_exists( 'palladio_vc_add_params_classes' ) ) {
	
	function palladio_vc_add_params_classes($classes, $sc, $atts) {
		if (in_array($sc, array('vc_section', 'vc_row', 'vc_row_inner', 'vc_column', 'vc_column_inner', 'vc_column_text'))) {
			if (!empty($atts['scheme']) && !palladio_is_inherit($atts['scheme']))
				$classes .= ($classes ? ' ' : '') . 'scheme_' . $atts['scheme'];
            if (!empty($atts['row_bg']) && !palladio_is_inherit($atts['row_bg']))
                $classes .= ($classes ? ' ' : '') . 'vc_row_bg';
		} else if (in_array($sc, array('vc_empty_space'))) {
			if (!empty($atts['alter_height']) && !palladio_is_off($atts['alter_height']))
				$classes .= ($classes ? ' ' : '') . 'height_' . $atts['alter_height'];
			if (!empty($atts['hide_on_mobile'])) {
				if (strpos($atts['hide_on_mobile'], '1')!==false)	$classes .= ($classes ? ' ' : '') . 'hide_on_mobile';
				if (strpos($atts['hide_on_mobile'], '2')!==false)	$classes .= ($classes ? ' ' : '') . 'hide_on_notebook';
				if (strpos($atts['hide_on_mobile'], '3')!==false)	$classes .= ($classes ? ' ' : '') . 'hide_on_tablet';
			}
		} else if (in_array($sc, array('vc_progress_bar'))) {
			if (!empty($atts['narrow']) && (int) $atts['narrow']==1)
				$classes .= ($classes ? ' ' : '') . 'vc_progress_bar_narrow';
		} else if (in_array($sc, array('vc_message'))) {
			if (!empty($atts['closeable']) && (int) $atts['closeable']==1)
				$classes .= ($classes ? ' ' : '') . 'vc_message_box_closeable';
		}
		return $classes;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if (palladio_exists_visual_composer()) { require_once PALLADIO_THEME_DIR . 'plugins/js_composer/js_composer.styles.php'; }
?>