<?php
/**
 * Shortcode: Display WooCommerce cart with items number and totals
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.08
 */

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_layouts_cart_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_layouts_cart_load_scripts_front');
	function trx_addons_sc_layouts_cart_load_scripts_front() {
		if (function_exists('trx_addons_exists_woocommerce') && trx_addons_exists_woocommerce()) {
			if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
				wp_enqueue_style( 'trx_addons-sc_layouts_cart', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart.css'), array(), null );
			}
		}
	}
}

	
// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_cart_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_cart_merge_styles');
	function trx_addons_sc_layouts_cart_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart.css';
		return $list;
	}
}

	
// Merge shortcode's specific scripts into single file
if ( !function_exists( 'trx_addons_sc_layouts_cart_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_layouts_cart_merge_scripts');
	function trx_addons_sc_layouts_cart_merge_scripts($list) {
		$list[] = TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart.js';
		return $list;
	}
}



// trx_sc_layouts_cart
//-------------------------------------------------------------
/*
[trx_sc_layouts_cart id="unique_id" text="Shopping cart"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_cart' ) ) {
	function trx_addons_sc_layouts_cart($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_cart', $atts, array(
			// Individual params
			"type" => "default",
			"text" => "",
			"hide_on_tablet" => "0",
			"hide_on_mobile" => "0",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);

		if (trx_addons_is_on(trx_addons_get_option('debug_mode')))
			wp_enqueue_script( 'trx_addons-sc_layouts_cart', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart.js'), array('jquery'), null, true );

		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/tpl.default.php'
										),
										'trx_addons_args_sc_layouts_cart',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();

		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_cart', $atts, $content);
	}
}


// Add [trx_sc_layouts_cart] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_cart_add_in_vc')) {
	function trx_addons_sc_layouts_cart_add_in_vc() {

		add_shortcode("trx_sc_layouts_cart", "trx_addons_sc_layouts_cart");

		if (!trx_addons_exists_visual_composer()) return;
	
		vc_lean_map("trx_sc_layouts_cart", 'trx_addons_sc_layouts_cart_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Cart extends WPBakeryShortCode {}

	}
	add_action('init', 'trx_addons_sc_layouts_cart_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_cart_add_in_vc_params')) {
	function trx_addons_sc_layouts_cart_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_cart",
				"name" => esc_html__("Layouts: Cart", 'trx_addons'),
				"description" => wp_kses_data( __("Insert cart with items number and totals to the custom layout", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_cart',
				"class" => "trx_sc_layouts_cart",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons'),
							), 'trx_sc_layouts_cart')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "text",
							"heading" => esc_html__("Cart text", 'trx_addons'),
							"description" => wp_kses_data( __("Text before cart", 'trx_addons') ),
							"admin_label" => true,
							"value" => "",
							"type" => "textfield"
						)
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_cart');
	}
}



// SOW Widget
//------------------------------------------------------
if (class_exists('TRX_Addons_SOW_Widget')
	//&& function_exists('trx_addons_exists_woocommerce') && trx_addons_exists_woocommerce()
	) {

	class TRX_Addons_SOW_Widget_Layouts_Cart extends TRX_Addons_SOW_Widget {
		
		function __construct() {
			parent::__construct(
				'trx_addons_sow_widget_layouts_cart',
				esc_html__('ThemeREX Addons SOW - Layouts: Cart', 'trx_addons'),
				array(
					'classname' => 'widget_layouts_cart',
					'description' => __('Insert cart with items number and totals to the custom layout', 'trx_addons')
				),
				array(),
				false,
				TRX_ADDONS_PLUGIN_DIR
			);
	
		}


		// Return array with all widget's fields
		function get_widget_form() {
			return apply_filters('trx_addons_sow_map', array_merge(
				array(
					'type' => array(
						'label' => __('Layout', 'trx_addons'),
						"description" => wp_kses_data( __("Select shortcodes's type", 'trx_addons') ),
						'default' => 'default',
						'options' => apply_filters('trx_addons_sc_type', array(
							'default' => esc_html__('Default', 'trx_addons')
						), $this->get_sc_name()),
						'type' => 'select'
					),
					"text" => array(
						"label" => esc_html__("Cart text", 'trx_addons'),
						"description" => wp_kses_data( __("Text before cart", 'trx_addons') ),
						"type" => "text"
					),
				),
				trx_addons_sow_add_hide_param(),
				trx_addons_sow_add_id_param()
			), $this->get_sc_name());
		}

	}
	siteorigin_widget_register('trx_addons_sow_widget_layouts_cart', __FILE__, 'TRX_Addons_SOW_Widget_Layouts_Cart');
}
?>