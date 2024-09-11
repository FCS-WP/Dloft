<?php
/* Revolution Slider support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('palladio_revslider_theme_setup9')) {
	add_action( 'after_setup_theme', 'palladio_revslider_theme_setup9', 9 );
	function palladio_revslider_theme_setup9() {
		if (palladio_exists_revslider()) {
			add_action( 'wp_enqueue_scripts', 					'palladio_revslider_frontend_scripts', 1100 );
			add_filter( 'palladio_filter_merge_styles',			'palladio_revslider_merge_styles' );
		}
		if (is_admin()) {
			add_filter( 'palladio_filter_tgmpa_required_plugins','palladio_revslider_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'palladio_revslider_tgmpa_required_plugins' ) ) {
	
	function palladio_revslider_tgmpa_required_plugins($list=array()) {
		if (palladio_storage_isset('required_plugins', 'revslider')) {
			$path = palladio_get_file_dir('plugins/revslider/revslider.zip');
			$list[] = array(
					'name' 		=> palladio_storage_get_array('required_plugins', 'revslider'),
					'slug' 		=> 'revslider',
					'version'	=> '6.6.15',
					'source'	=> !empty($path) ? $path : 'upload://revslider.zip',
					'required' 	=> false
			);
		}
		return $list;
	}
}

// Check if RevSlider installed and activated
if ( !function_exists( 'palladio_exists_revslider' ) ) {
	function palladio_exists_revslider() {
		return function_exists('rev_slider_shortcode');
	}
}
	
// Enqueue custom styles
if ( !function_exists( 'palladio_revslider_frontend_scripts' ) ) {
	
	function palladio_revslider_frontend_scripts() {
		if (palladio_is_on(palladio_get_theme_option('debug_mode')) && palladio_get_file_dir('plugins/revslider/revslider.css')!='')
			wp_enqueue_style( 'palladio-revslider',  palladio_get_file_url('plugins/revslider/revslider.css'), array(), null );
	}
}
	
// Merge custom styles
if ( !function_exists( 'palladio_revslider_merge_styles' ) ) {
	
	function palladio_revslider_merge_styles($list) {
		$list[] = 'plugins/revslider/revslider.css';
		return $list;
	}
}
?>