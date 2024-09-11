<?php
/**
 * Shortcode: Display Login link
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.08
 */

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_layouts_login_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_layouts_login_load_scripts_front');
	function trx_addons_sc_layouts_login_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_layouts_login', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'login/login.css'), array(), null );
		}
	}
}

	
// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_login_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_login_merge_styles');
	function trx_addons_sc_layouts_login_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'login/login.css';
		return $list;
	}
}



// trx_sc_layouts_login
//-------------------------------------------------------------
/*
[trx_sc_layouts_login id="unique_id" text="Link text" title="link title"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_login' ) ) {
	function trx_addons_sc_layouts_login($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_login', $atts, array(
			// Individual params
			"type" => "default",
			"text_login" => "",
			"text_logout" => "",
			"hide_on_tablet" => "0",
			"hide_on_mobile" => "0",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);

		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'login/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'login/tpl.default.php'
										),
										'trx_addons_args_sc_layouts_login',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_login', $atts, $content);
	}
}


// Add [trx_sc_layouts_login] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_login_add_in_vc')) {
	function trx_addons_sc_layouts_login_add_in_vc() {
		
		add_shortcode("trx_sc_layouts_login", "trx_addons_sc_layouts_login");

		if (!trx_addons_exists_visual_composer()) return;

		vc_lean_map("trx_sc_layouts_login", 'trx_addons_sc_layouts_login_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Login extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_login_add_in_vc', 15);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_login_add_in_vc_params')) {
	function trx_addons_sc_layouts_login_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_login",
				"name" => esc_html__("Layouts: Login link", 'trx_addons'),
				"description" => wp_kses_data( __("Insert Login/Logout link to the custom layout", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_login',
				"class" => "trx_sc_layouts_login",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							"admin_label" => true,
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons'),
							), 'trx_sc_layouts_login')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "text_login",
							"heading" => esc_html__("Login text", 'trx_addons'),
							"description" => wp_kses_data( __("Text of the Login link", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							"param_name" => "text_logout",
							"heading" => esc_html__("Logout text", 'trx_addons'),
							"description" => wp_kses_data( __("Text of the Logout link", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						)
					),
					trx_addons_vc_add_hide_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_login');
	}
}



// SOW Widget
//------------------------------------------------------
if (class_exists('TRX_Addons_SOW_Widget')) {
	class TRX_Addons_SOW_Widget_Layouts_Login extends TRX_Addons_SOW_Widget {
		
		function __construct() {
			parent::__construct(
				'trx_addons_sow_widget_layouts_login',
				esc_html__('ThemeREX Addons SOW - Layouts: Login', 'trx_addons'),
				array(
					'classname' => 'widget_layouts_login',
					'description' => __('Insert Login/Logout link to the custom layout', 'trx_addons')
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
					"text_login" => array(
						"label" => esc_html__("Login text", 'trx_addons'),
						"description" => wp_kses_data( __("Text of the Login link", 'trx_addons') ),
						"type" => "text"
					),
					"text_logout" => array(
						"label" => esc_html__("Logout text", 'trx_addons'),
						"description" => wp_kses_data( __("Text of the Logout link", 'trx_addons') ),
						"type" => "text"
					)
				),
				trx_addons_sow_add_hide_param(),
				trx_addons_sow_add_id_param()
			), $this->get_sc_name());
		}

	}
	siteorigin_widget_register('trx_addons_sow_widget_layouts_login', __FILE__, 'TRX_Addons_SOW_Widget_Layouts_Login');
}
?>