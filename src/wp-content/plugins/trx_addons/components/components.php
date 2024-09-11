<?php
/**
 * ThemeREX Addons Pluggable modules (Components)
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.29
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}


if (!defined('TRX_ADDONS_PLUGIN_API'))				define('TRX_ADDONS_PLUGIN_API', 			TRX_ADDONS_PLUGIN_COMPONENTS.'api/');
if (!defined('TRX_ADDONS_PLUGIN_CPT'))				define('TRX_ADDONS_PLUGIN_CPT',				TRX_ADDONS_PLUGIN_COMPONENTS.'cpt/');
if (!defined('TRX_ADDONS_PLUGIN_CV'))				define('TRX_ADDONS_PLUGIN_CV',				TRX_ADDONS_PLUGIN_COMPONENTS.'cv/');
if (!defined('TRX_ADDONS_PLUGIN_DEMO'))				define('TRX_ADDONS_PLUGIN_DEMO',			TRX_ADDONS_PLUGIN_COMPONENTS.'demo/');
if (!defined('TRX_ADDONS_PLUGIN_EDITOR'))			define('TRX_ADDONS_PLUGIN_EDITOR',			TRX_ADDONS_PLUGIN_COMPONENTS.'editor/');
if (!defined('TRX_ADDONS_PLUGIN_IMPORTER'))			define('TRX_ADDONS_PLUGIN_IMPORTER',		TRX_ADDONS_PLUGIN_COMPONENTS.'importer/');
if (!defined('TRX_ADDONS_PLUGIN_SHORTCODES'))		define('TRX_ADDONS_PLUGIN_SHORTCODES',		TRX_ADDONS_PLUGIN_COMPONENTS.'shortcodes/');
if (!defined('TRX_ADDONS_PLUGIN_THEMES_MARKET'))	define('TRX_ADDONS_PLUGIN_THEMES_MARKET',	TRX_ADDONS_PLUGIN_COMPONENTS.'themes_market/');
if (!defined('TRX_ADDONS_PLUGIN_WIDGETS'))			define('TRX_ADDONS_PLUGIN_WIDGETS', 		TRX_ADDONS_PLUGIN_COMPONENTS.'widgets/');


// Third-party plugins support
if (file_exists(TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'api.php')) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'api.php';
}

// Custom post types
if (file_exists(TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'cpt.php')) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'cpt.php';
}

// CV card
if (file_exists(TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CV . 'cv.php')) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CV . 'cv.php';
}

// Editor extensions
if (file_exists(TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_EDITOR . 'editor.php')) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_EDITOR . 'editor.php';
}

// Demo site mode
if (file_exists(TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_DEMO . 'demo.php')) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_DEMO . 'demo.php';
}

// Demo data import/export
if (file_exists(TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_IMPORTER . 'importer.php')) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_IMPORTER . 'importer.php';
}

// Shortcodes
if (file_exists(TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'shortcodes.php')) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'shortcodes.php';
}

// Theme market
if (file_exists(TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_THEMES_MARKET . 'edd.themes_market.php')) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_THEMES_MARKET . 'edd.themes_market.php';
}

// Widgets
if (file_exists(TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'widgets.php')) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_WIDGETS . 'widgets.php';
}


// Add 'Other Components' block in the ThemeREX Addons Components
if (!function_exists('trx_addons_components_components')) {
	add_filter( 'trx_addons_filter_components_blocks', 'trx_addons_components_components');
	function trx_addons_components_components($blocks=array()) {
		$blocks['components'] = __('Other components', 'trx_addons');
		return $blocks;
	}
}

// Define list with components
if (!function_exists('trx_addons_components_setup')) {
	add_action( 'after_setup_theme', 'trx_addons_components_setup', 2 );
	add_action( 'trx_addons_action_save_options', 'trx_addons_components_setup', 2 );
	function trx_addons_components_setup() {
		static $loaded = false;
		if ($loaded) return;
		$loaded = true;
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['components_list'] = apply_filters('trx_addons_components_list', array(

			'cv' => array(
					'title' => __('CV Card', 'trx_addons')
					),
			'editor' => array(
					'title' => __('WP Editor extensions', 'trx_addons')
					),
			'importer' => array(
					'title' => __('Import/Export demo data', 'trx_addons'),
					'std' => 1,
					'hidden' => true
					),
			'demo' => array(
					'title' => __('Demo-site mode', 'trx_addons')
					),
			'themes_market' => array(
					'title' => __('Themes market', 'trx_addons')
					)
			)
		);
	}
}
?>