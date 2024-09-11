<?php
/**
 * The template to display menu in the footer
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0.10
 */

// Footer menu
$palladio_menu_footer = palladio_get_nav_menu(array(
											'location' => 'menu_footer',
											'class' => 'sc_layouts_menu sc_layouts_menu_default'
											));
if (!empty($palladio_menu_footer)) {
	?>
	<div class="footer_menu_wrap">
		<div class="footer_menu_inner">
			<?php palladio_show_layout($palladio_menu_footer); ?>
		</div>
	</div>
	<?php
}
?>