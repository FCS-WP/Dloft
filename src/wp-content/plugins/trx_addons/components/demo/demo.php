<?php
/**
 * Demo mode support
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.29
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Add 'Demo' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_demo_options')) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_demo_options');
	function trx_addons_demo_options($options) {
		
		if (trx_addons_components_is_allowed('components', 'demo') && apply_filters('trx_addons_demo_enable', false)) {

			global $TRX_ADDONS_STORAGE;
	
			trx_addons_array_insert_before($options, 'theme_specific_section', array(
			
				// Contacts - address, phone, email, etc.
				'demo_section' => array(
					"title" => esc_html__('Demo', 'trx_addons'),
					"desc" => wp_kses_data( __('Demo mode parameters', 'trx_addons') ),
					"type" => "section"
				),
				'demo_enable' => array(
					"title" => esc_html__('Enable Demo mode', 'trx_addons'),
					"desc" => wp_kses_data( __('Enable redirect to the demo page on this site', 'trx_addons') ),
					"std" => "0",
					"type" => "checkbox"
				),
				'demo_referer' => array(
					"title" => esc_html__("Referer", 'trx_addons'),
					"desc" => wp_kses_data( __("Enter parts of the address to determine whether to redirect the browser to our demo page. Start each part of the referer's url from a new line", 'trx_addons') ),
					"dependency" => array(
						"demo_enable" => array(1)
					),
					"std" => '',
					"type" => "textarea"
				),
				'demo_default_url' => array(
					"title" => esc_html__('Default URL with all demo products', 'trx_addons'),
					"desc" => wp_kses_data( __('Specify URL with all demo products to redirect if demo address is incorrect', 'trx_addons') ),
					"dependency" => array(
						"demo_enable" => array(1)
					),
					"std" => "",
					"type" => "text"
				),
				'demo_logo' => array(
					"title" => esc_html__('Logo in the top-left corner', 'trx_addons'),
					"desc" => wp_kses_data( __('Select logo-image to place it in the top-left corner of the demo frame', 'trx_addons') ),
					"dependency" => array(
						"demo_enable" => array(1)
					),
					"std" => "",
					"type" => "image"
				),
				'demo_post_type' => array(
					"title" => esc_html__("Post type", 'trx_addons'),
					"desc" => wp_kses_data( __("Select post type to show demo elements", 'trx_addons') ),
					"dependency" => array(
						"demo_enable" => array(1)
					),
					"std" => 'product',
					"type" => "post_type"
				),
				'demo_taxonomy' => array(
					"title" => esc_html__("Taxonomy", 'trx_addons'),
					"desc" => wp_kses_data( __("Select taxonomy to filter related elements", 'trx_addons') ),
					"dependency" => array(
						"demo_enable" => array(1)
					),
					"std" => 'product_tag',
					"type" => "taxonomy"
				)
			) );

		}
		
		return $options;
	}
}



// Include files with Demo
if (!function_exists('trx_addons_demo_load')) {
	add_action( 'after_setup_theme', 'trx_addons_demo_load', 6 );
	add_action( 'trx_addons_action_save_options', 'trx_addons_demo_load', 6 );
	function trx_addons_demo_load() {
		static $loaded = false;
		if ($loaded) return;
		$loaded = true;
		if (trx_addons_components_is_allowed('components', 'demo') 
			&& apply_filters('trx_addons_demo_enable', trx_addons_is_on(trx_addons_get_option('demo_enable', false, false)))) {
			if (($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_DEMO . 'includes/demo.php')) != '') {
				include_once $fdir;
			}
		}
	}
}
?>