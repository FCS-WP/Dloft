<?php
/* Mail Chimp support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('palladio_mailchimp_theme_setup9')) {
	add_action( 'after_setup_theme', 'palladio_mailchimp_theme_setup9', 9 );
	function palladio_mailchimp_theme_setup9() {
		if (palladio_exists_mailchimp()) {
			add_action( 'wp_enqueue_scripts',							'palladio_mailchimp_frontend_scripts', 1100 );
			add_filter( 'palladio_filter_merge_styles',					'palladio_mailchimp_merge_styles');
		}
		if (is_admin()) {
			add_filter( 'palladio_filter_tgmpa_required_plugins',		'palladio_mailchimp_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'palladio_mailchimp_tgmpa_required_plugins' ) ) {
	
	function palladio_mailchimp_tgmpa_required_plugins($list=array()) {
		if (palladio_storage_isset('required_plugins', 'mailchimp-for-wp')) {
			$list[] = array(
				'name' 		=> palladio_storage_get_array('required_plugins', 'mailchimp-for-wp'),
				'slug' 		=> 'mailchimp-for-wp',
				'required' 	=> false
			);
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( !function_exists( 'palladio_exists_mailchimp' ) ) {
	function palladio_exists_mailchimp() {
		return function_exists('__mc4wp_load_plugin') || defined('MC4WP_VERSION');
	}
}



// Custom styles and scripts
//------------------------------------------------------------------------

// Enqueue custom styles
if ( !function_exists( 'palladio_mailchimp_frontend_scripts' ) ) {
	
	function palladio_mailchimp_frontend_scripts() {
		if (palladio_exists_mailchimp()) {
			if (palladio_is_on(palladio_get_theme_option('debug_mode')) && palladio_get_file_dir('plugins/mailchimp-for-wp/mailchimp-for-wp.css')!='')
				wp_enqueue_style( 'palladio-mailchimp-for-wp',  palladio_get_file_url('plugins/mailchimp-for-wp/mailchimp-for-wp.css'), array(), null );
		}
	}
}
	
// Merge custom styles
if ( !function_exists( 'palladio_mailchimp_merge_styles' ) ) {
	
	function palladio_mailchimp_merge_styles($list) {
		$list[] = 'plugins/mailchimp-for-wp/mailchimp-for-wp.css';
		return $list;
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if (palladio_exists_mailchimp()) { require_once PALLADIO_THEME_DIR . 'plugins/mailchimp-for-wp/mailchimp-for-wp.styles.php'; }
?>