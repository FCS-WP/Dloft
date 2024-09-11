<?php
/**
 * The template to display the socials in the footer
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0.10
 */


// Socials
if ( palladio_is_on(palladio_get_theme_option('socials_in_footer')) && ($palladio_output = palladio_get_socials_links()) != '') {
	?>
	<div class="footer_socials_wrap socials_wrap">
		<div class="footer_socials_inner">
			<?php palladio_show_layout($palladio_output); ?>
		</div>
	</div>
	<?php
}
?>