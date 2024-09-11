<?php
/* Instagram Feed support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('palladio_instagram_feed_theme_setup9')) {
	add_action( 'after_setup_theme', 'palladio_instagram_feed_theme_setup9', 9 );
	function palladio_instagram_feed_theme_setup9() {
		if (is_admin()) {
			add_filter( 'palladio_filter_tgmpa_required_plugins',		'palladio_instagram_feed_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'palladio_instagram_feed_tgmpa_required_plugins' ) ) {
	
	function palladio_instagram_feed_tgmpa_required_plugins($list=array()) {
		if (palladio_storage_isset('required_plugins', 'instagram-feed')) {
			$list[] = array(
					'name' 		=> palladio_storage_get_array('required_plugins', 'instagram-feed'),
					'slug' 		=> 'instagram-feed',
					'required' 	=> false
				);
		}
		return $list;
	}
}

// Check if Instagram Feed installed and activated
if ( !function_exists( 'palladio_exists_instagram_feed' ) ) {
	function palladio_exists_instagram_feed() {
		return defined('SBIVER');
	}
}
?>
