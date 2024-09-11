<?php
/**
 * Demo mode functions and handlers
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.29
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

if ( ! defined( 'TRX_ADDONS_DEMO_PARAM' ) )	define( 'TRX_ADDONS_DEMO_PARAM', 'demo' );

// Check if current page is demo page
if (!function_exists('trx_addons_is_demo_page')) {
	function trx_addons_is_demo_page() {
		return trx_addons_is_on(trx_addons_get_option('demo_enable')) && trx_addons_get_value_gp(TRX_ADDONS_DEMO_PARAM)!='';
	}
}


// Check if need redirect to the demo page
if (!function_exists('trx_addons_demo_need_redirect')) {
	function trx_addons_demo_need_redirect() {
		$rez = false;
		if (!empty($_SERVER['HTTP_REFERER'])) {
			$referer = explode('|', str_replace(array("\n", "\r"), array('|', ''), trx_addons_get_option('demo_referer')));
			foreach ($referer as $r) {
				if (strpos($_SERVER['HTTP_REFERER'], $r)!==false) {
					$rez = true;
					break;
				}
			}
		}
		return $rez;
	}
}


// Return link of the demo page
if (!function_exists('trx_addons_get_demo_page_link')) {
	function trx_addons_get_demo_page_link($demo, $params=array()) {
		$params[TRX_ADDONS_DEMO_PARAM] = $demo;
		return trx_addons_add_to_url(home_url(), $params);
	}
}


// Add styles to the head
if (!function_exists('trx_addons_demo_head')) {
	add_action( 'trx_addons_demo_head', 'trx_addons_demo_head');
	function trx_addons_demo_head() {
		?><link href="<?php echo esc_url(trx_addons_get_file_url(TRX_ADDONS_PLUGIN_DEMO . 'css/demo.css')); ?>" rel="stylesheet"><?php
	}
}


// Add scripts to the footer
if (!function_exists('trx_addons_demo_footer')) {
	add_action( 'trx_addons_demo_footer', 'trx_addons_demo_footer');
	function trx_addons_demo_footer() {
		?>
		<script id="jquery" src="<?php echo esc_url(get_admin_url(null, 'load-scripts.php?c=1&load[]=jquery-core,jquery-migrate')); ?>"></script>
		<script id="jquery-qrcode" src="<?php echo esc_url(trx_addons_get_file_url(TRX_ADDONS_PLUGIN_DEMO . 'js/jquery-qrcode-0.14.0.min.js')); ?>"></script>
		<script id="trx_addons_demo" src="<?php echo esc_url(trx_addons_get_file_url(TRX_ADDONS_PLUGIN_DEMO . 'js/demo.js')); ?>"></script>
		<?php
	}
}


// Redirect to the demo template
if (!function_exists('trx_addons_demo_get_single_template')) {
	add_filter('home_template', 'trx_addons_demo_get_template');
	add_filter('page_template', 'trx_addons_demo_get_template');
	add_filter('single_template', 'trx_addons_demo_get_template');
	function trx_addons_demo_get_template($template) {
		if (trx_addons_is_demo_page())
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_DEMO . 'templates/demo.tpl.php');
		return $template;
	}
}
?>