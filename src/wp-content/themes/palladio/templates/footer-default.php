<?php
/**
 * The template to display default site footer
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0.10
 */

$palladio_footer_scheme =  palladio_is_inherit(palladio_get_theme_option('footer_scheme')) ? palladio_get_theme_option('color_scheme') : palladio_get_theme_option('footer_scheme');
?>
<footer class="footer_wrap footer_default scheme_<?php echo esc_attr($palladio_footer_scheme); ?>">
	<?php

	// Footer widgets area
	get_template_part( 'templates/footer-widgets' );

	// Logo
	get_template_part( 'templates/footer-logo' );

	// Socials
	get_template_part( 'templates/footer-socials' );

	// Menu
	get_template_part( 'templates/footer-menu' );

	// Copyright area
	get_template_part( 'templates/footer-copyright' );
	
	?>
</footer><!-- /.footer_wrap -->
