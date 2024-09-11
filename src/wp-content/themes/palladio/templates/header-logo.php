<?php
/**
 * The template to display the logo or the site name and the slogan in the Header
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

$palladio_args = get_query_var('palladio_logo_args');

// Site logo
$palladio_logo_image  = palladio_get_logo_image(isset($palladio_args['type']) ? $palladio_args['type'] : '');
$palladio_logo_text   = palladio_is_on(palladio_get_theme_option('logo_text')) ? get_bloginfo( 'name' ) : '';
$palladio_logo_slogan = get_bloginfo( 'description', 'display' );
if (!empty($palladio_logo_image) || !empty($palladio_logo_text)) {
	?><a class="sc_layouts_logo" href="<?php echo is_front_page() ? '#' : esc_url(home_url('/')); ?>"><?php
		if (!empty($palladio_logo_image)) {
			$palladio_attr = palladio_getimagesize($palladio_logo_image);
			echo '<img src="'.esc_url($palladio_logo_image).'" '.(!empty($palladio_attr[3]) ? sprintf(' %s', $palladio_attr[3]) : '').'>';
		} else {
			palladio_show_layout(palladio_prepare_macros($palladio_logo_text), '<span class="logo_text">', '</span>');
			palladio_show_layout(palladio_prepare_macros($palladio_logo_slogan), '<span class="logo_slogan">', '</span>');
		}
	?></a><?php
}
?>