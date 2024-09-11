<?php
/* Essential Grid support functions
------------------------------------------------------------------------------- */


// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if (!function_exists('palladio_essential_grid_theme_setup9')) {
	add_action( 'after_setup_theme', 'palladio_essential_grid_theme_setup9', 9 );
	function palladio_essential_grid_theme_setup9() {
		if (palladio_exists_essential_grid()) {
			add_action( 'wp_enqueue_scripts', 							'palladio_essential_grid_frontend_scripts', 1100 );
			add_filter( 'palladio_filter_merge_styles',					'palladio_essential_grid_merge_styles' );
		}
		if (is_admin()) {
			add_filter( 'palladio_filter_tgmpa_required_plugins',		'palladio_essential_grid_tgmpa_required_plugins' );
		}
	}
}

// Filter to add in the required plugins list
if ( !function_exists( 'palladio_essential_grid_tgmpa_required_plugins' ) ) {
	
	function palladio_essential_grid_tgmpa_required_plugins($list=array()) {
		if (palladio_storage_isset('required_plugins', 'essential-grid')) {
			$path = palladio_get_file_dir('plugins/essential-grid/essential-grid.zip');
			$list[] = array(
						'name' 		=> palladio_storage_get_array('required_plugins', 'essential-grid'),
						'slug' 		=> 'essential-grid',
						'version'	=> '3.0.19',
						'source'	=> !empty($path) ? $path : 'upload://essential-grid.zip',
						'required' 	=> false
			);
		}
		return $list;
	}
}

// Check if plugin installed and activated
if ( !function_exists( 'palladio_exists_essential_grid' ) ) {
	function palladio_exists_essential_grid() {
		return defined( 'EG_PLUGIN_PATH' ) || defined( 'ESG_PLUGIN_PATH' );
	}
}
	
// Enqueue plugin's custom styles
if ( !function_exists( 'palladio_essential_grid_frontend_scripts' ) ) {
	
	function palladio_essential_grid_frontend_scripts() {
		if (palladio_is_on(palladio_get_theme_option('debug_mode')) && palladio_get_file_dir('plugins/essential-grid/essential-grid.css')!='')
			wp_enqueue_style( 'palladio-essential-grid',  palladio_get_file_url('plugins/essential-grid/essential-grid.css'), array(), null );
	}
}
	
// Merge custom styles
if ( !function_exists( 'palladio_essential_grid_merge_styles' ) ) {
	
	function palladio_essential_grid_merge_styles($list) {
		$list[] = 'plugins/essential-grid/essential-grid.css';
		return $list;
	}
}
?>