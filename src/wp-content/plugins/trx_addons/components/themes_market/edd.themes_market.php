<?php
/**
 * Plugin support: Easy Digital Downloads (Themes market support)
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.29
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// -----------------------------------------------------------------
// -- Additional taxonomies and post's meta
// -----------------------------------------------------------------

if (!defined('TRX_ADDONS_EDD_PT')) define('TRX_ADDONS_EDD_PT', 'download');
if (!defined('TRX_ADDONS_EDD_TAXONOMY_CATEGORY')) define('TRX_ADDONS_EDD_TAXONOMY_CATEGORY', 'download_category');
if (!defined('TRX_ADDONS_EDD_TAXONOMY_TAG')) define('TRX_ADDONS_EDD_TAXONOMY_TAG', 'download_tag');
if (!defined('TRX_ADDONS_EDD_TAXONOMY_COMPATIBILITY') ) define('TRX_ADDONS_EDD_TAXONOMY_COMPATIBILITY', 'download_compatibility');
if (!defined('TRX_ADDONS_EDD_TAXONOMY_BROWSERS') ) define('TRX_ADDONS_EDD_TAXONOMY_BROWSERS', 'download_browsers');
if (!defined('TRX_ADDONS_EDD_TAXONOMY_PACKAGE') ) define('TRX_ADDONS_EDD_TAXONOMY_PACKAGE', 'download_package');
if (!defined('TRX_ADDONS_EDD_TAXONOMY_PLUGINS') ) define('TRX_ADDONS_EDD_TAXONOMY_PLUGINS', 'download_plugins');

// Check if plugin installed and activated
if ( !function_exists( 'trx_addons_exists_edd' ) ) {
	function trx_addons_exists_edd() {
		return class_exists('Easy_Digital_Downloads');
	}
}

// Check if Themes Market is enabled
if (!function_exists('trx_addons_edd_themes_market_enable')) {
	function trx_addons_edd_themes_market_enable() {
		static $enable = null;
		if ($enable === null) {
			$enable = trx_addons_components_is_allowed('components', 'themes_market') 
											&& trx_addons_exists_edd()
											&& apply_filters('trx_addons_filter_edd_themes_market', false);
		}
		return $enable;
	}
}

// Add 'Themes Market' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_edd_themes_market_options')) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_edd_themes_market_options');
	function trx_addons_edd_themes_market_options($options) {
		
		if (trx_addons_edd_themes_market_enable()) {

			global $TRX_ADDONS_STORAGE;
	
			trx_addons_array_insert_after($options, 'api_section', array(
			
				// Contacts - address, phone, email, etc.
				'api_themes_market_info' => array(
					"title" => esc_html__('Themes Market', 'trx_addons'),
					"desc" => wp_kses_data( __("Affiliate parameters from marketplaces", 'trx_addons') ),
					"type" => "info"
				),
				'themes_market_referals' => array(
					"title" => esc_html__("Referals", 'trx_addons'),
					"desc" => wp_kses_data( __("Affiliate parameters from marketplaces", 'trx_addons') ),
					"clone" => true,
					"std" => array(array()),
					"type" => "group",
					"fields" => array(
						'url' => array(
							"title" => esc_html__("Part of the marketplace's URL", 'trx_addons'),
							"desc" => wp_kses_data( __("If product's URL have this substring - next param should be added", 'trx_addons') ),
							"class" => "trx_addons_column-1_2 trx_addons_new_row",
							"std" => "",
							"type" => "text"
						),
						'param' => array(
							"title" => esc_html__('Parameters to add', 'trx_addons'),
							"desc" => wp_kses_data( __("Parameters to add to the URL (as key1=value1&key2=value2...)", 'trx_addons') ),
							"class" => "trx_addons_column-1_2",
							"std" => "",
							"type" => "text"
						)
					)
				)
			) );

		}
		
		return $options;
	}
}


// Register additional taxonomies
if (!function_exists('trx_addons_edd_themes_market_init')) {
	add_action( 'init', 'trx_addons_edd_themes_market_init' );
	function trx_addons_edd_themes_market_init() {

		if (!trx_addons_edd_themes_market_enable()) return;

		// Add Downloads parameters to the Meta Box support
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['post_types'][] = TRX_ADDONS_EDD_PT;
		$TRX_ADDONS_STORAGE['meta_box_'.TRX_ADDONS_EDD_PT] = array(
			"general_section" => array(
				"title" => esc_html__("General", 'trx_addons'),
				"desc" => wp_kses_data( __('General options', 'trx_addons') ),
				"type" => "section"
			),
			"slug" => array(
				"title" => esc_html__("Slug", 'trx_addons'),
				"desc" => wp_kses_data( __('Slug to create the demo link', 'trx_addons') ),
				"std" => '',
				"type" => "text"
			),
			"date_created" => array(
				"title" => esc_html__("Date created", 'trx_addons'),
				"desc" => wp_kses_data( __('The creation date of the item in the format "YYYY-mm-dd"', 'trx_addons') ),
				"std" => date('Y-m-d'),
				"type" => "date"
			),
			"date_updated" => array(
				"title" => esc_html__("Last update", 'trx_addons'),
				"desc" => wp_kses_data( __('Date of last update of this item in the format "YYYY-mm-dd"', 'trx_addons') ),
				"std" => date('Y-m-d'),
				"type" => "date"
			),
			"version" => array(
				"title" => esc_html__("Version", 'trx_addons'),
				"desc" => wp_kses_data( __("Current version of this product", 'trx_addons') ),
				"std" => '1.0',
				"type" => "text"
			),
			"demo_url" => array(
				"title" => esc_html__("Product preview URL", 'trx_addons'),
				"desc" => wp_kses_data( __("Specify URL of the item's demo site", 'trx_addons') ),
				"std" => '',
				"type" => "text"
			),
			"download_url" => array(
				"title" => esc_html__("Product download URL", 'trx_addons'),
				"desc" => wp_kses_data( __("The URL for downloading this item, if this item placed on some marketplace. If empty - internal shop is used to sale this item", 'trx_addons') ),
				"std" => '',
				"type" => "text"
			),
			"doc_url" => array(
				"title" => esc_html__("Online documentation URL", 'trx_addons'),
				"desc" => wp_kses_data( __("Specify URL of the item's online documentation", 'trx_addons') ),
				"std" => '',
				"type" => "text"
			),
			"features_section" => array(
				"title" => esc_html__("Features", 'trx_addons'),
				"desc" => wp_kses_data( __('Main features', 'trx_addons') ),
				"type" => "section"
			),
			"retina" => array(
				"title" => esc_html__("Retina ready", 'trx_addons'),
				"desc" => wp_kses_data( __("High resolution ready", 'trx_addons') ),
				"std" => 1,
				"type" => "checkbox"
			),
			"responsive" => array(
				"title" => esc_html__("Responsive", 'trx_addons'),
				"desc" => wp_kses_data( __("Are responsive styles and layouts included?", 'trx_addons') ),
				"std" => 1,
				"type" => "checkbox"
			),
			"columns" => array(
				"title" => esc_html__("Columns", 'trx_addons'),
				"desc" => wp_kses_data( __("Columns number in the layouts of this item", 'trx_addons') ),
				"std" => '4+',
				"options" => array(
					'1' => esc_html__('1 column', 'trx_addons'),
					'2' => esc_html__('2 columns', 'trx_addons'),
					'3' => esc_html__('3 columns', 'trx_addons'),
					'4+' => esc_html__('4+ columns', 'trx_addons')
				),
				"dir" => "horizontal",
				"type" => "radio"
			),
			"widgets" => array(
				"title" => esc_html__("Widgets", 'trx_addons'),
				"desc" => wp_kses_data( __("If there are widgets in the package", 'trx_addons') ),
				"std" => '10+',
				"options" => array(
					'none' => esc_html__('No widgets', 'trx_addons'),
					'up_5' => esc_html__('Up to 5', 'trx_addons'),
					'up_10' => esc_html__('Up to 10', 'trx_addons'),
					'10+' => esc_html__('More than 10', 'trx_addons')
				),
				"dir" => "horizontal",
				"type" => "radio"
			),
			"shortcodes" => array(
				"title" => esc_html__("Shortcodes", 'trx_addons'),
				"desc" => wp_kses_data( __("If there are shortcodes in the package", 'trx_addons') ),
				"std" => '20+',
				"options" => array(
					'none' => esc_html__('No shortcodes', 'trx_addons'),
					'up_10' => esc_html__('Up to 10', 'trx_addons'),
					'up_20' => esc_html__('Up to 20', 'trx_addons'),
					'20+' => esc_html__('More than 20', 'trx_addons')
				),
				"dir" => "horizontal",
				"type" => "radio"
			),
			"support" => array(
				"title" => esc_html__("Support", 'trx_addons'),
				"desc" => wp_kses_data( __("Support type of this item", 'trx_addons') ),
				"std" => 'standard',
				"options" => array(
					'none' => esc_html__('No support', 'trx_addons'),
					'standard' => esc_html__('30 days', 'trx_addons'),
					'premium' => esc_html__('Premium', 'trx_addons')
				),
				"type" => "select"
			),
			"documentation" => array(
				"title" => esc_html__("Documentation", 'trx_addons'),
				"desc" => wp_kses_data( __("Documentation of this item", 'trx_addons') ),
				"std" => 'well',
				"options" => array(
					'none' => esc_html__('None', 'trx_addons'),
					'medium' => esc_html__('Medium', 'trx_addons'),
					'well' => esc_html__('Well documented', 'trx_addons')
				),
				"type" => "select"
			),

			"additional_section" => array(
				"title" => esc_html__('Additional features', 'trx_addons'),
				"desc" => wp_kses_data( __('Additional (custom) features for this download', 'trx_addons') ),
				"type" => "section"
			),
			"details" => array(
				"title" => esc_html__("Additional features", 'trx_addons'),
				"desc" => wp_kses_data( __("Add more features for this download by pair title-value", 'trx_addons') ),
				"clone" => true,
				"std" => array(array()),
				"type" => "group",
				"fields" => array(
					"title" => array(
						"title" => esc_html__("Title", 'trx_addons'),
						"desc" => wp_kses_data( __('Current feature title', 'trx_addons') ),
						"class" => "trx_addons_column-1_2",
						"std" => "",
						"type" => "text"
					),
					"value" => array(
						"title" => esc_html__("Value", 'trx_addons'),
						"desc" => wp_kses_data( __('Current feature value', 'trx_addons') ),
						"class" => "trx_addons_column-1_2",
						"std" => "",
						"type" => "text"
					)
				)
			),
		);
		
		// Compatibility with different products: WP, WooCommerce, RevSlider, etc.
		register_taxonomy( TRX_ADDONS_EDD_TAXONOMY_COMPATIBILITY, TRX_ADDONS_EDD_PT, array(
			'post_type' 		=> TRX_ADDONS_EDD_PT,
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => esc_html__( 'Compatibilities', 'trx_addons' ),
				'singular_name'     => esc_html__( 'Compatibility', 'trx_addons' ),
				'search_items'      => esc_html__( 'Search Compatibilities', 'trx_addons' ),
				'all_items'         => esc_html__( 'All Compatibilities', 'trx_addons' ),
				'parent_item'       => esc_html__( 'Parent Compatibility', 'trx_addons' ),
				'parent_item_colon' => esc_html__( 'Parent Compatibility:', 'trx_addons' ),
				'edit_item'         => esc_html__( 'Edit Compatibility', 'trx_addons' ),
				'update_item'       => esc_html__( 'Update Compatibility', 'trx_addons' ),
				'add_new_item'      => esc_html__( 'Add New Compatibility', 'trx_addons' ),
				'new_item_name'     => esc_html__( 'New Compatibility Name', 'trx_addons' ),
				'menu_name'         => esc_html__( 'Compatibilities', 'trx_addons' ),
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true
			)
		);

		// Compatibility with different browsers: IE9, IE10, IE11, Firefox, Chrome, Opera, Safari, etc.
		register_taxonomy( TRX_ADDONS_EDD_TAXONOMY_BROWSERS, TRX_ADDONS_EDD_PT, array(
			'post_type' 		=> TRX_ADDONS_EDD_PT,
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => esc_html__( 'Browsers', 'trx_addons' ),
				'singular_name'     => esc_html__( 'Browser', 'trx_addons' ),
				'search_items'      => esc_html__( 'Search Browsers', 'trx_addons' ),
				'all_items'         => esc_html__( 'All Browsers', 'trx_addons' ),
				'parent_item'       => esc_html__( 'Parent Browser', 'trx_addons' ),
				'parent_item_colon' => esc_html__( 'Parent Browser:', 'trx_addons' ),
				'edit_item'         => esc_html__( 'Edit Browser', 'trx_addons' ),
				'update_item'       => esc_html__( 'Update Browser', 'trx_addons' ),
				'add_new_item'      => esc_html__( 'Add New Browser', 'trx_addons' ),
				'new_item_name'     => esc_html__( 'New Browser Name', 'trx_addons' ),
				'menu_name'         => esc_html__( 'Browsers', 'trx_addons' ),
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true
			)
		);

		// Plugins presents in the package
		register_taxonomy( TRX_ADDONS_EDD_TAXONOMY_PLUGINS, TRX_ADDONS_EDD_PT, array(
			'post_type' 		=> TRX_ADDONS_EDD_PT,
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => esc_html__( 'Plugins in the package', 'trx_addons' ),
				'singular_name'     => esc_html__( 'Plugin', 'trx_addons' ),
				'search_items'      => esc_html__( 'Search Plugins', 'trx_addons' ),
				'all_items'         => esc_html__( 'All Plugins', 'trx_addons' ),
				'parent_item'       => esc_html__( 'Parent Plugin', 'trx_addons' ),
				'parent_item_colon' => esc_html__( 'Parent Plugin:', 'trx_addons' ),
				'edit_item'         => esc_html__( 'Edit Plugin', 'trx_addons' ),
				'update_item'       => esc_html__( 'Update Plugin', 'trx_addons' ),
				'add_new_item'      => esc_html__( 'Add New Plugin', 'trx_addons' ),
				'new_item_name'     => esc_html__( 'New Plugin Name', 'trx_addons' ),
				'menu_name'         => esc_html__( 'Plugins in the package', 'trx_addons' ),
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true
			)
		);

		// Attachments presents in the package
		register_taxonomy( TRX_ADDONS_EDD_TAXONOMY_PACKAGE, TRX_ADDONS_EDD_PT, array(
			'post_type' 		=> TRX_ADDONS_EDD_PT,
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => esc_html__( 'The Package', 'trx_addons' ),
				'singular_name'     => esc_html__( 'Package', 'trx_addons' ),
				'search_items'      => esc_html__( 'Search Packages', 'trx_addons' ),
				'all_items'         => esc_html__( 'All Packages', 'trx_addons' ),
				'parent_item'       => esc_html__( 'Parent Package', 'trx_addons' ),
				'parent_item_colon' => esc_html__( 'Parent Package:', 'trx_addons' ),
				'edit_item'         => esc_html__( 'Edit Package', 'trx_addons' ),
				'update_item'       => esc_html__( 'Update Package', 'trx_addons' ),
				'add_new_item'      => esc_html__( 'Add new Package', 'trx_addons' ),
				'new_item_name'     => esc_html__( 'New Package Name', 'trx_addons' ),
				'menu_name'         => esc_html__( 'The Package', 'trx_addons' ),
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true
			)
		);

	}
}


// Save download's dates for search, sorting, etc.
if ( !function_exists( 'trx_addons_edd_themes_market_save_post_options' ) ) {
	add_filter('trx_addons_filter_save_post_options', 'trx_addons_edd_themes_market_save_post_options', 10, 3);
	function trx_addons_edd_themes_market_save_post_options($options, $post_id, $post_type) {
		if ($post_type == TRX_ADDONS_EDD_PT && trx_addons_edd_themes_market_enable()) { 
			update_post_meta($post_id, 'trx_addons_edd_slug', $options['slug']);
			update_post_meta($post_id, 'trx_addons_edd_date_created', $options['date_created']);
			update_post_meta($post_id, 'trx_addons_edd_date_updated', $options['date_updated']);
		}
		return $options;
	}
}


// Show details of the current product in the single post
if ( !function_exists( 'trx_addons_edd_themes_market_after_download_content' ) ) {
	add_action( 'edd_after_download_content', 'trx_addons_edd_themes_market_after_download_content', 9, 1 );
	function trx_addons_edd_themes_market_after_download_content($post_id=0) {
		if (is_single() && get_post_type()==TRX_ADDONS_EDD_PT && trx_addons_edd_themes_market_enable()) {
			// Show download's details after the content if shortcode 'trx_sc_edd_details' is not present in the content
			if (strpos(get_the_content(), '[trx_sc_edd_details')===false) {
				require_once trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_THEMES_MARKET . 'tpl.edd_details.default.php');
			}
			// Remove 'Buy' link after the download content if this download placed on the external marketplace
			$trx_addons_meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
			if (!empty($trx_addons_meta['download_url'])) {
				remove_action( 'edd_after_download_content', 'edd_append_purchase_link' );
				?><div class="trx_addons_buttons edd_download_purchase_form"><?php
					if (!empty($trx_addons_meta['demo_url'])) {
						?><a href="<?php
								echo esc_url(defined('TRX_ADDONS_DEMO_PARAM')
												? trx_addons_add_to_url(home_url(), array(TRX_ADDONS_DEMO_PARAM => $trx_addons_meta['slug']))
												: $trx_addons_meta['demo_url']
											); ?>" class="sc_button" target="_blank"><?php
								esc_html_e('Live demo', 'trx_addons');
						?></a><?php
					}
					?><a href="<?php echo esc_url(trx_addons_add_referals_to_url($trx_addons_meta['download_url'],
												  trx_addons_get_option('themes_market_referals')));
							?>" class="sc_button" target="_blank"><?php
							echo sprintf(esc_html__('%s - Purchase', 'trx_addons'), edd_price(get_the_ID(), false));
					?></a>
				</div><?php
			}
		}
	}
}


// Show demo-link before the 'Buy now' button
if ( !function_exists( 'trx_addons_edd_themes_market_purchase_link_top' ) ) {
	add_action( 'edd_purchase_link_top', 'trx_addons_edd_themes_market_purchase_link_top', 10, 2 );
	function trx_addons_edd_themes_market_purchase_link_top($post_id=0, $args=array()) {
	    if (!trx_addons_edd_themes_market_enable()) return;
		$trx_addons_meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
		if (!empty($trx_addons_meta['demo_url'])) {
			?><a href="<?php echo esc_url(defined('TRX_ADDONS_DEMO_PARAM')
												? trx_addons_add_to_url(home_url(), array(TRX_ADDONS_DEMO_PARAM => $trx_addons_meta['slug']))
												: $trx_addons_meta['demo_url']
											); ?>" class="sc_button" target="_blank"><?php
						esc_html_e('Live demo', 'trx_addons');
			?></a><?php
		}
	}
}


// Show purchase key in the View Order Details
if ( !function_exists( 'trx_addons_edd_themes_market_payment_receipt_after' ) ) {
	add_action( 'edd_payment_receipt_after', 'trx_addons_edd_themes_market_payment_receipt_after', 10, 2 );
	function trx_addons_edd_themes_market_payment_receipt_after($payment, $args) {
	    if (!trx_addons_edd_themes_market_enable()) return;
		$meta = edd_get_payment_meta( $payment->ID );
		if (!empty($meta['key'])) {
			?>
			<tr>
				<th class="edd_receipt_payment_key"><strong><?php esc_html_e( 'Purchase Key', 'trx_addons' ); ?>:</strong></th>
				<th class="edd_receipt_payment_key"><?php echo esc_html($meta['key']); ?></th>
			</tr>
			<?php
		}
	}
}


// Add class 'download_market_[internal|external]' to the <article> on the single page
if ( !function_exists( 'trx_addons_edd_themes_market_post_class' ) ) {
	add_filter( 'post_class', 'trx_addons_edd_themes_market_post_class', 11 );
	function trx_addons_edd_themes_market_post_class($classes) {
		if (get_post_type() == TRX_ADDONS_EDD_PT && trx_addons_edd_themes_market_enable()) {
			$trx_addons_meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
			$classes[] = 'download_market_'.(!empty($trx_addons_meta['download_url']) ? 'external' : 'internal');
		}
		return $classes;
	}
}


// Load required scripts and styles
//------------------------------------------------------------------------
	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_edd_themes_market_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_edd_themes_market_load_scripts_front', 11);
	function trx_addons_edd_themes_market_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode')) && trx_addons_edd_themes_market_enable()) {
			wp_enqueue_style( 'trx_addons-edd_themes_market', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_THEMES_MARKET . 'edd.themes_market.css'), array(), null );
		}
	}
}

	
// Merge specific styles into single stylesheet
if ( !function_exists( 'trx_addons_edd_themes_market_merge_styles' ) ) {
	add_action("trx_addons_filter_merge_styles", 'trx_addons_edd_themes_market_merge_styles');
	function trx_addons_edd_themes_market_merge_styles($list) {
	    if (trx_addons_edd_themes_market_enable())	$list[] = TRX_ADDONS_PLUGIN_THEMES_MARKET . 'edd.themes_market.css';
		return $list;
	}
}



// Admin utils
// -----------------------------------------------------------------

// Create additional column in the posts list
if (!function_exists('trx_addons_edd_themes_market_add_custom_column')) {
	add_filter('manage_edit-'.TRX_ADDONS_EDD_PT.'_columns',	'trx_addons_edd_themes_market_add_custom_column', 11);
	function trx_addons_edd_themes_market_add_custom_column( $columns ){
		if (trx_addons_edd_themes_market_enable()) {
			if (is_array($columns) && count($columns)>0) {
				$new_columns = array();
				foreach($columns as $k=>$v) {
					if ($k=='price')
						$new_columns['edd_slug'] = esc_html__('Slug', 'trx_addons');
					$new_columns[$k] = $v;
				}
				$columns = $new_columns;
			}
	    }
		return $columns;
	}
}

// Fill custom columns in the posts list
if (!function_exists('trx_addons_edd_themes_market_fill_custom_column')) {
	add_action('manage_'.TRX_ADDONS_EDD_PT.'_posts_custom_column', 'trx_addons_edd_themes_market_fill_custom_column', 11, 2);
	function trx_addons_edd_themes_market_fill_custom_column($column_name='', $post_id=0) {
		if ($column_name == 'edd_slug') {
			$slug = get_post_meta($post_id, 'trx_addons_edd_slug', true);
			if (!empty($slug)) {
				?><div class="trx_addons_meta_row">
					<span class="trx_addons_meta_label"><?php echo esc_html($slug); ?></span>
				</div><?php
			}
		}
	}
}


// trx_sc_edd_details
//-------------------------------------------------------------
/*
[trx_sc_edd_details id="unique_id" type="default"]
*/
if ( !function_exists( 'trx_addons_sc_edd_details' ) ) {
	function trx_addons_sc_edd_details($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_edd_details', $atts, array(
			// Individual params
			"type" => "default",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			));

		$atts['class'] .= ($atts['class'] ? ' ' : '') . 'sc_edd_details';

		ob_start();
		trx_addons_get_template_part(TRX_ADDONS_PLUGIN_THEMES_MARKET . 'tpl.edd_details.'.trx_addons_esc($atts['type']).'.php',
									'trx_addons_args_sc_edd_details',
									$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_edd_details', $atts, $content);
	}
}


// Add [trx_sc_edd_details] in the VC shortcodes list
if (!function_exists('trx_addons_sc_edd_details_add_in_vc')) {
	function trx_addons_sc_edd_details_add_in_vc() {

		add_shortcode("trx_sc_edd_details", "trx_addons_sc_edd_details");

		if (!trx_addons_exists_visual_composer() || !trx_addons_edd_themes_market_enable()) return;

		vc_lean_map( "trx_sc_edd_details", 'trx_addons_sc_edd_details_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Edd_Details extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_edd_details_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_edd_details_add_in_vc_params')) {
	function trx_addons_sc_edd_details_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_edd_details",
				"name" => esc_html__("EDD Details", 'trx_addons'),
				"description" => wp_kses_data( __("Display current download's details", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_edd_details',
				"class" => "trx_sc_edd_details",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							"admin_label" => true,
							"std" => "default",
					        'save_always' => true,
							"value" => array_flip(apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons')
							), 'trx_sc_edd_details')),
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_edd_details' );
	}
}
?>