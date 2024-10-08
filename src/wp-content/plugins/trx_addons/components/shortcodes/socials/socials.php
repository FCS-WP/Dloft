<?php
/**
 * Shortcode: Socials
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_socials_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_socials_load_scripts_front');
	function trx_addons_sc_socials_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_socials', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'socials/socials.css'), array(), null );
		}
	}
}
	
// Merge contact form specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_socials_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_socials_merge_styles');
	function trx_addons_sc_socials_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_SHORTCODES . 'socials/socials.css';
		return $list;
	}
}



// trx_sc_socials
//-------------------------------------------------------------
/*
[trx_sc_socials id="unique_id" icons="encoded_json_data"]
*/
if ( !function_exists( 'trx_addons_sc_socials' ) ) {
	function trx_addons_sc_socials($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_socials', $atts, array(
			// Individual params
			"type" => "default",
			"icons" => "",
			"align" => "",
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"link" => '',
			"link_image" => '',
			"link_text" => esc_html__('Learn more', 'trx_addons'),
			"title_align" => "left",
			"title_style" => "default",
			"title_tag" => '',
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);

		if (function_exists('vc_param_group_parse_atts') && !is_array($atts['icons']))
			$atts['icons'] = (array) vc_param_group_parse_atts($atts['icons']);

		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_SHORTCODES . 'socials/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_SHORTCODES . 'socials/tpl.default.php'
										),
                                        'trx_addons_args_sc_socials',
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_socials', $atts, $content);
	}
}


// Add [trx_sc_socials] in the VC shortcodes list
if (!function_exists('trx_addons_sc_socials_add_in_vc')) {
	function trx_addons_sc_socials_add_in_vc() {
		
		add_shortcode("trx_sc_socials", "trx_addons_sc_socials");
		
		if (!trx_addons_exists_visual_composer()) return;
		
		vc_lean_map("trx_sc_socials", 'trx_addons_sc_socials_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Socials extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_socials_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_socials_add_in_vc_params')) {
	function trx_addons_sc_socials_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_socials",
				"name" => esc_html__("Socials", 'trx_addons'),
				"description" => wp_kses_data( __("Insert social icons with links on your profiles", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_socials',
				"class" => "trx_sc_socials",
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
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'socials'), 'trx_sc_socials')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Icons alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of the icons", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "default",
							"value" => array_flip(trx_addons_get_list_sc_title_aligns()),
							"type" => "dropdown"
						),
						array(
							'type' => 'param_group',
							'param_name' => 'icons',
							'heading' => esc_html__( 'Icons', 'trx_addons' ),
							"description" => wp_kses_data( __("Select social icons and specify link for each item", 'trx_addons') ),
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
								array(
									'link' => '',
									'icon_image' => '',
									'icon' => '',
									'icon_fontawesome' => 'empty',
									'icon_openiconic' => 'empty',
									'icon_typicons' => 'empty',
									'icon_entypo' => 'empty',
									'icon_linecons' => 'empty'
								),
							), 'trx_sc_socials') ) ),
							'params' => apply_filters('trx_addons_sc_param_group_params',
									array_merge(
										array(
											array(
												'param_name' => 'title',
												'heading' => esc_html__( 'Title', 'trx_addons' ),
												'description' => esc_html__( 'Name of the social network', 'trx_addons' ),
												'edit_field_class' => 'vc_col-sm-6',
												'admin_label' => true,
												'type' => 'textfield',
											),
											array(
												'param_name' => 'link',
												'heading' => esc_html__( 'Link', 'trx_addons' ),
												'description' => esc_html__( 'URL to your profile', 'trx_addons' ),
												'edit_field_class' => 'vc_col-sm-6',
												'admin_label' => true,
												'type' => 'textfield',
											)
										),
										trx_addons_vc_add_icon_param('', true)
									),
									'trx_sc_socials')
						)
					),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_socials' );
	}
}




// SOW Widget
//------------------------------------------------------
if (class_exists('TRX_Addons_SOW_Widget')) {
	class TRX_Addons_SOW_Widget_Socials extends TRX_Addons_SOW_Widget {
		
		function __construct() {
			parent::__construct(
				'trx_addons_sow_widget_socials',
				esc_html__('ThemeREX Addons - Socials (Custom)', 'trx_addons'),
				array(
					'classname' => 'widget_socials',
					'description' => __('Links to your favorite social networks', 'trx_addons')
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
						"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
						'default' => 'default',
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'socials'), $this->get_sc_name(), 'sow'),
						'type' => 'select'
					),
					'align' => array(
						'label' => __('Icons alignment', 'trx_addons'),
						"description" => wp_kses_data( __("Select alignment of the icons", 'trx_addons') ),
						'default' => 'default',
						'options' => trx_addons_get_list_sc_title_aligns(),
						'type' => 'select'
					),
					'icons' => array(
						'label' => __('Icons', 'trx_addons'),
						'item_name'  => __( 'Social network params', 'trx_addons' ),
						'item_label' => array(
							'selector'     => "[name*='title']",
							'update_event' => 'change',
							'value_method' => 'val'
						),
						'type' => 'repeater',
						'fields' => apply_filters('trx_addons_sc_param_group_fields', array_merge(array(
								'title' => array(
									'label' => __('Title', 'trx_addons'),
									'description' => esc_html__( 'Name of the social network', 'trx_addons' ),
									'type' => 'text'
								),
								'link' => array(
									'label' => __('Link', 'trx_addons'),
									'description' => esc_html__( 'URL to your profile in this network', 'trx_addons' ),
									'type' => 'text'
								)
							),
							trx_addons_sow_add_icon_param('', true)
						), $this->get_sc_name())
					)
				),
				trx_addons_sow_add_title_param(),
				trx_addons_sow_add_id_param()
			), $this->get_sc_name());
		}

	}
	siteorigin_widget_register('trx_addons_sow_widget_socials', __FILE__, 'TRX_Addons_SOW_Widget_Socials');
}
?>