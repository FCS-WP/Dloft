<?php
/**
 * Plugin support: Easy Digital Downloads
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


// Check if plugin installed and activated
if ( !function_exists( 'trx_addons_exists_edd' ) ) {
	function trx_addons_exists_edd() {
		return class_exists('Easy_Digital_Downloads');
	}
}

// Return true, if current page is any edd page
if ( !function_exists( 'trx_addons_is_edd_page' ) ) {
	function trx_addons_is_edd_page() {
		$rez = trx_addons_exists_edd()
					&& !is_search()
					&& (
						(is_single() && get_post_type()==TRX_ADDONS_EDD_PT)
						|| is_post_type_archive(TRX_ADDONS_EDD_PT)
						|| is_tax(TRX_ADDONS_EDD_TAXONOMY_CATEGORY)
						|| is_tax(TRX_ADDONS_EDD_TAXONOMY_TAG)
						);
		return $rez;
	}
}


// Return taxonomy for current post type
if ( !function_exists( 'trx_addons_edd_post_type_taxonomy' ) ) {
	add_filter( 'trx_addons_filter_post_type_taxonomy',	'trx_addons_edd_post_type_taxonomy', 10, 2 );
	function trx_addons_edd_post_type_taxonomy($tax='', $post_type='') {
		if ($post_type == TRX_ADDONS_EDD_PT)
			$tax = TRX_ADDONS_EDD_TAXONOMY_CATEGORY;
		return $tax;
	}
}

// Return link to main shop page for the breadcrumbs
if ( !function_exists( 'trx_addons_edd_get_blog_all_posts_link' ) ) {
	add_filter('trx_addons_filter_get_blog_all_posts_link', 'trx_addons_edd_get_blog_all_posts_link', 10, 2);
	function trx_addons_edd_get_blog_all_posts_link($link='', $args=array()) {
		if (empty($link) && trx_addons_is_edd_page() && !is_post_type_archive(TRX_ADDONS_EDD_PT)) {
			$link = '<a href="'.esc_url(get_post_type_archive_link( TRX_ADDONS_EDD_PT )).'">'
							. esc_html__('All Downloads', 'trx_addons')
					. '</a>';
		}
		return $link;
	}
}

// Return current page title
if ( !function_exists( 'trx_addons_edd_get_blog_title' ) ) {
	add_filter( 'trx_addons_filter_get_blog_title', 'trx_addons_edd_get_blog_title');
	function trx_addons_edd_get_blog_title($title='') {
		if (trx_addons_exists_edd() && is_post_type_archive(TRX_ADDONS_EDD_PT) )
			$title = esc_html__('All Downloads', 'trx_addons');
		return $title;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_edd_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_edd_importer_required_plugins', 10, 2 );
	function trx_addons_edd_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'easy-digital-downloads')!==false && !trx_addons_exists_edd() )
			$not_installed .= '<br>' . esc_html__('Easy Digital Downloads', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_edd_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_options',	'trx_addons_edd_importer_set_options' );
	function trx_addons_edd_importer_set_options($options=array()) {
		if ( trx_addons_exists_edd() && in_array('easy-digital-downloads', $options['required_plugins']) ) {
			$options['additional_options'][] = 'edd_settings';					// Add slugs to export options for this plugin
			if (is_array($options['files']) && count($options['files']) > 0) {
				foreach ($options['files'] as $k => $v) {
					$options['files'][$k]['file_with_edd'] = str_replace('name.ext', 'easy-digital-downloads.txt', $v['file_with_']);
				}
			}
		}
		return $options;
	}
}

// Add checkbox to the one-click importer
if ( !function_exists( 'trx_addons_edd_importer_show_params' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_params',	'trx_addons_edd_importer_show_params', 10, 1 );
	function trx_addons_edd_importer_show_params($importer) {
		if ( trx_addons_exists_edd() && in_array('easy-digital-downloads', $importer->options['required_plugins']) ) {
			$importer->show_importer_params(array(
				'slug' => 'edd',
				'title' => esc_html__('Import Easy Digital Downloads', 'trx_addons'),
				'part' => 0
			));
		}
	}
}

// Import posts
if ( !function_exists( 'trx_addons_edd_importer_import' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_import',	'trx_addons_edd_importer_import', 10, 2 );
	function trx_addons_edd_importer_import($importer, $action) {
		if ( trx_addons_exists_edd() && in_array('easy-digital-downloads', $importer->options['required_plugins']) ) {
			if ( $action == 'import_edd' ) {
				$importer->response['start_from_id'] = 0;
				$importer->import_dump('easy-digital-downloads', esc_html__('Easy Digital Downloads meta', 'trx_addons'));
			}
		}
	}
}

// Check if the row will be imported
if ( !function_exists( 'trx_addons_edd_importer_check_row' ) ) {
	if (is_admin()) add_filter('trx_addons_filter_importer_import_row', 'trx_addons_edd_importer_check_row', 9, 4);
	function trx_addons_edd_importer_check_row($flag, $table, $row, $list) {
		if ($flag || strpos($list, 'easy-digital-downloads')===false) return $flag;
		if ( trx_addons_exists_edd() ) {
			if ($table == 'posts')
				$flag = in_array($row['post_type'], array('download', 'edd_log', 'edd_discount', 'edd_payment'));
		}
		return $flag;
	}
}

// Display import progress
if ( !function_exists( 'trx_addons_edd_importer_import_fields' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_import_fields',	'trx_addons_edd_importer_import_fields', 10, 1 );
	function trx_addons_edd_importer_import_fields($importer) {
		if ( trx_addons_exists_edd() && in_array('easy-digital-downloads', $importer->options['required_plugins']) ) {
			$importer->show_importer_fields(array(
				'slug'=>'edd', 
				'title' => esc_html__('Easy Digital Downloads meta', 'trx_addons')
				)
			);
		}
	}
}

// Export posts
if ( !function_exists( 'trx_addons_edd_importer_export' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_export',	'trx_addons_edd_importer_export', 10, 1 );
	function trx_addons_edd_importer_export($importer) {
		if ( trx_addons_exists_edd() && in_array('easy-digital-downloads', $importer->options['required_plugins']) ) {
			trx_addons_fpc(trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_IMPORTER . 'export/easy-digital-downloads.txt'), serialize( array(
				"edd_customers"		=> $importer->export_dump("edd_customers"),
				"edd_customermeta"	=> $importer->export_dump("edd_customermeta"),
				) )
			);
		}
	}
}

// Display exported data in the fields
if ( !function_exists( 'trx_addons_edd_importer_export_fields' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_export_fields',	'trx_addons_edd_importer_export_fields', 10, 1 );
	function trx_addons_edd_importer_export_fields($importer) {
		if ( trx_addons_exists_edd() && in_array('easy-digital-downloads', $importer->options['required_plugins']) ) {
			$importer->show_exporter_fields(array(
				'slug'	=> 'edd',
				'title' => esc_html__('Easy Digital Downloads', 'trx_addons')
				)
			);
		}
	}
}
?>