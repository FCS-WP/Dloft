<?php
/**
 * The template to display default site footer
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0.10
 */

$palladio_footer_scheme =  palladio_is_inherit(palladio_get_theme_option('footer_scheme')) ? palladio_get_theme_option('color_scheme') : palladio_get_theme_option('footer_scheme');
$palladio_footer_id = str_replace('footer-custom-', '', palladio_get_theme_option("footer_style"));
if ((int) $palladio_footer_id == 0) {
	$palladio_footer_id = palladio_get_post_id(array(
												'name' => $palladio_footer_id,
												'post_type' => defined('TRX_ADDONS_CPT_LAYOUT_PT') ? TRX_ADDONS_CPT_LAYOUT_PT : 'cpt_layouts'
												)
											);
} else {
    $palladio_footer_id = apply_filters('trx_addons_filter_get_translated_layout', $palladio_footer_id);
}
$palladio_footer_meta = get_post_meta($palladio_footer_id, 'trx_addons_options', true);
?>
<footer class="footer_wrap footer_custom footer_custom_<?php echo esc_attr($palladio_footer_id); 
						?> footer_custom_<?php echo esc_attr(sanitize_title(get_the_title($palladio_footer_id))); 
						if (!empty($palladio_footer_meta['margin']) != '') 
							echo ' '.esc_attr(palladio_add_inline_css_class('margin-top: '.esc_attr(palladio_prepare_css_value($palladio_footer_meta['margin'])).';'));
						?> scheme_<?php echo esc_attr($palladio_footer_scheme); 
						?>">
	<?php
    // Custom footer's layout
    do_action('palladio_action_show_layout', $palladio_footer_id);
	?>
</footer><!-- /.footer_wrap -->
