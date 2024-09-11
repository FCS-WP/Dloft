<?php
/**
 * The template to display the site logo in the footer
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0.10
 */

// Logo
if (palladio_is_on(palladio_get_theme_option('logo_in_footer'))) {
	$palladio_logo_image = '';
	if (palladio_get_retina_multiplier(2) > 1)
		$palladio_logo_image = palladio_get_theme_option( 'logo_footer_retina' );
	if (empty($palladio_logo_image)) 
		$palladio_logo_image = palladio_get_theme_option( 'logo_footer' );
	$palladio_logo_text   = get_bloginfo( 'name' );
	if (!empty($palladio_logo_image) || !empty($palladio_logo_text)) {
		?>
		<div class="footer_logo_wrap">
			<div class="footer_logo_inner">
				<?php
				if (!empty($palladio_logo_image)) {
					$palladio_attr = palladio_getimagesize($palladio_logo_image);
					echo '<a href="'.esc_url(home_url('/')).'"><img src="'.esc_url($palladio_logo_image).'" class="logo_footer_image" '.(!empty($palladio_attr[3]) ? sprintf(' %s', $palladio_attr[3]) : '').'></a>' ;
				} else if (!empty($palladio_logo_text)) {
					echo '<h1 class="logo_footer_text"><a href="'.esc_url(home_url('/')).'">' . esc_html($palladio_logo_text) . '</a></h1>';
				}
				?>
			</div>
		</div>
		<?php
	}
}
?>