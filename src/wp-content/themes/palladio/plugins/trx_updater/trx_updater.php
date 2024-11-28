<?php
/* TRX Updater support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('palladio_trx_updater_theme_setup9')) {
	add_action( 'after_setup_theme', 'palladio_trx_updater_theme_setup9', 9 );
	function palladio_trx_updater_theme_setup9() {
		if (is_admin()) {
			add_filter( 'palladio_filter_tgmpa_required_plugins','palladio_trx_updater_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'palladio_trx_updater_tgmpa_required_plugsins' ) ) {
	
	function palladio_trx_updater_tgmpa_required_plugins($list=array()) {
		if (palladio_storage_isset('required_plugins', 'trx_updater')) {
			$path = palladio_get_file_dir('plugins/trx_updater/trx_updater.zip');
			if (!empty($path) || palladio_get_theme_setting('tgmpa_upload')) {
				$list[] = array(
					'name' 		=> palladio_storage_get_array('required_plugins', 'trx_updater'),
					'slug' 		=> 'trx_updater',
					'version'	=> '2.1.0',
					'source'	=> !empty($path) ? $path : 'upload://trx_updater.zip',
					'required' 	=> false
				);
			}
		}
		return $list;
	}
}

// Check if this plugin installed and activated
if ( !function_exists( 'palladio_exists_trx_updater' ) ) {
	function palladio_exists_trx_updater() {
		return function_exists( 'trx_updater_load_plugin_textdomain' );
	}
}
?>