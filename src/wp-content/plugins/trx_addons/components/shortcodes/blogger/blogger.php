<?php
/**
 * Shortcode: Blogger
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_blogger_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_blogger_load_scripts_front');
	function trx_addons_sc_blogger_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_blogger', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/blogger.css'), array(), null );
		}
	}
}

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_blogger_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_sc_blogger_merge_styles');
	function trx_addons_sc_blogger_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/blogger.css';
		return $list;
	}
}



// trx_sc_blogger
//-------------------------------------------------------------
/*
[trx_sc_blogger id="unique_id" type="default" cat="category_slug or id" count="3" columns="3" slider="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_blogger' ) ) {
	function trx_addons_sc_blogger($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_blogger', $atts, array(
			// Individual params
			"type" => 'default',
			"hide_excerpt" => 0,
			'post_type' => 'post',
			'taxonomy' => 'category',
			"cat" => '',
			"count" => 3,
			"columns" => '',
			"offset" => 0,
			"orderby" => '',
			"order" => '',
			"ids" => '',
			"slider" => 0,
			"slider_pagination" => "none",
			"slider_controls" => "none",
			"slides_space" => 0,
			"title" => '',
			"subtitle" => '',
			"description" => '',
			"link" => '',
			"link_image" => '',
			"link_text" => esc_html__('Learn more', 'trx_addons'),
			"title_align" => 'left',
			"title_style" => 'default',
			"title_tag" => '',
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		
		if (!empty($atts['ids'])) {
			$atts['ids'] = str_replace(array(';', ' '), array(',', ''), $atts['ids']);
			$atts['count'] = count(explode(',', $atts['ids']));
		}
		$atts['count'] = max(1, (int) $atts['count']);
		$atts['offset'] = max(0, (int) $atts['offset']);
		if (empty($atts['orderby'])) $atts['orderby'] = 'date';
		if (empty($atts['order'])) $atts['order'] = 'desc';
		$atts['slider'] = max(0, (int) $atts['slider']);
		if ($atts['slider'] > 0 && (int) $atts['slider_pagination'] > 0) $atts['slider_pagination'] = 'bottom';

		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/tpl.default.php'
										),
                                        'trx_addons_args_sc_blogger', 
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_blogger', $atts, $content);
	}
}


// Add [trx_sc_blogger] in the VC shortcodes list
if (!function_exists('trx_addons_sc_blogger_add_in_vc')) {
	function trx_addons_sc_blogger_add_in_vc() {

		add_shortcode("trx_sc_blogger", "trx_addons_sc_blogger");
		
		if (!trx_addons_exists_visual_composer()) return;
		
		vc_lean_map("trx_sc_blogger", 'trx_addons_sc_blogger_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Blogger extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_blogger_add_in_vc', 20);
}


// Return params
if (!function_exists('trx_addons_sc_blogger_add_in_vc_params')) {
	function trx_addons_sc_blogger_add_in_vc_params() {
		// If open params in VC Editor
		list($vc_edit, $vc_params) = trx_addons_get_vc_form_params('trx_sc_blogger');
		// Prepare lists
		$post_type = $vc_edit && !empty($vc_params['post_type']) ? $vc_params['post_type'] : 'post';
		$taxonomy = $vc_edit && !empty($vc_params['taxonomy']) ? $vc_params['taxonomy'] : 'category';
		$taxonomies_objects = get_object_taxonomies($post_type, 'objects');
		$taxonomies = array();
		if (is_array($taxonomies_objects)) {
			foreach ($taxonomies_objects as $slug=>$taxonomy_obj) {
				$taxonomies[$slug] = $taxonomy_obj->label;
			}
		}
		$tax_obj = get_taxonomy($taxonomy);
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_blogger",
				"name" => esc_html__("Blogger", 'trx_addons'),
				"description" => wp_kses_data( __("Display posts from specified category in many styles", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_blogger',
				"class" => "trx_sc_blogger",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
/*
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							"admin_label" => true,
					        'save_always' => true,
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'blogger'), 'trx_sc_blogger')),
							"type" => "dropdown"
						),
*/
						// Attention! It's our custom param's type and it need values list as normal associative array 'key' => 'value'
						// not in VC-style 'value' => 'key'
						// 'style' => 'icons' | 'images'
						// 'mode' => 'inline' | 'dropdown'
						// 'return' => 'slug' | 'full'
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							"admin_label" => true,
					        'save_always' => true,
							"std" => "default",
							"value" => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'blogger'), 'trx_sc_blogger' ),
							"mode" => 'inline',
							"return" => 'slug',
							"style" => "images",
							"type" => "icons"
						),
						array(
							"param_name" => "hide_excerpt",
							"heading" => esc_html__("Excerpt", 'trx_addons'),
							"description" => wp_kses_data( __("Check if you want hide excerpt", 'trx_addons') ),
							'dependency' => array(
								'element' => 'type',
								'value' => array('classic')
							),
							"std" => "0",
							"value" => array(esc_html__("Hide excerpt", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "post_type",
							"heading" => esc_html__("Post type", 'trx_addons'),
							"description" => wp_kses_data( __("Select post type to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 'post',
							"value" => array_flip(trx_addons_get_list_posts_types()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "taxonomy",
							"heading" => esc_html__("Taxonomy", 'trx_addons'),
							"description" => wp_kses_data( __("Select taxonomy to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 'category',
							"value" => array_flip($taxonomies),
							"type" => "dropdown"
						),
						array(
							"param_name" => "cat",
							"heading" => esc_html__("Category", 'trx_addons'),
							"description" => wp_kses_data( __("Select category to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 0,
							"value" => array_flip(trx_addons_array_merge(array(0=>sprintf(__('- %s -', 'trx_addons'), $tax_obj->label)),
																		 $taxonomy == 'category' 
																			? trx_addons_get_list_categories() 
																			: trx_addons_get_list_terms(false, $taxonomy)
																		)),
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_query_param(''),
					trx_addons_vc_add_slider_param(),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_blogger' );
	}
}




// SOW Widget
//------------------------------------------------------
if (class_exists('TRX_Addons_SOW_Widget')) {
	class TRX_Addons_SOW_Widget_Blogger extends TRX_Addons_SOW_Widget {
		
		function __construct() {
			parent::__construct(
				'trx_addons_sow_widget_blogger',
				esc_html__('ThemeREX Addons - Blogger', 'trx_addons'),
				array(
					'classname' => 'widget_blogger',
					'description' => __('Display blog posts', 'trx_addons')
				),
				array(),
				false,
				TRX_ADDONS_PLUGIN_DIR
			);
	
		}


		// Return array with all widget's fields
		function get_widget_form() {
			// Prepare lists
			list($vc_edit, $vc_params) = trx_addons_get_sow_form_params('TRX_Addons_SOW_Widget_Blogger');
			$post_type = $vc_edit && !empty($vc_params['post_type']) ? $vc_params['post_type'] : 'post';
			$taxonomy = $vc_edit && !empty($vc_params['taxonomy']) ? $vc_params['taxonomy'] : 'category';
			$taxonomies_objects = get_object_taxonomies($post_type, 'objects');
			$taxonomies = array();
			if (is_array($taxonomies_objects)) {
				foreach ($taxonomies_objects as $slug=>$taxonomy_obj) {
					$taxonomies[$slug] = $taxonomy_obj->label;
				}
			}
			$tax_obj = get_taxonomy($taxonomy);
			return apply_filters('trx_addons_sow_map', array_merge(
				array(
					'type' => array(
						'label' => __('Layout', 'trx_addons'),
						"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
						'default' => 'default',
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'blogger'), $this->get_sc_name(), 'sow' ),
						'state_emitter' => array(
							'callback' => 'conditional',
							'args'     => array(
								'use_type[default]: val=="default"',
								'use_type[hide]: val!="default"',
							)
						),
						"mode" => 'inline',
						"return" => 'slug',
						"style" => "images",
						'type' => 'icons'
					),
					"hide_excerpt" => array(
						"label" => esc_html__("Hide excerpt", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want hide excerpt", 'trx_addons') ),
						'state_handler' => array(
							"use_type[default]" => array('show'),
							"use_type[hide]" => array('hide')
						),
						"default" => false,
						"type" => "checkbox"
					),
					"post_type" => array(
						"label" => esc_html__("Post type", 'trx_addons'),
						"description" => wp_kses_data( __("Select post type to show posts", 'trx_addons') ),
						"default" => 'post',
						"options" => trx_addons_get_list_posts_types(),
						"type" => "select"
					),
					"taxonomy" => array(
						"label" => esc_html__("Taxonomy", 'trx_addons'),
						"description" => wp_kses_data( __("Select taxonomy to show posts", 'trx_addons') ),
						"default" => 'category',
						"options" => $taxonomies,
						"type" => "select_dynamic"
					),
					"cat" => array(
						"label" => esc_html__("Category", 'trx_addons'),
						"description" => wp_kses_data( __("Select category to show posts", 'trx_addons') ),
						"default" => 0,
						"options" => trx_addons_array_merge(array(0=>sprintf(__('- %s -', 'trx_addons'), $tax_obj->label)),
																	 $taxonomy == 'category' 
																		? trx_addons_get_list_categories() 
																		: trx_addons_get_list_terms(false, $taxonomy)
																	),
						"type" => "select_dynamic"
					)
				),
				trx_addons_sow_add_query_param(''),
				trx_addons_sow_add_slider_param(),
				trx_addons_sow_add_title_param(),
				trx_addons_sow_add_id_param()
			), $this->get_sc_name());
		}

	}
	siteorigin_widget_register('trx_addons_sow_widget_blogger', __FILE__, 'TRX_Addons_SOW_Widget_Blogger');
}
?>