<?php
/**
 * The template to display the side menu
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */
?>
<div class="menu_side_wrap scheme_<?php echo esc_attr(palladio_is_inherit(palladio_get_theme_option('menu_scheme')) 
																	? (palladio_is_inherit(palladio_get_theme_option('header_scheme')) 
																		? palladio_get_theme_option('color_scheme') 
																		: palladio_get_theme_option('header_scheme')) 
																	: palladio_get_theme_option('menu_scheme'));
			echo " menu_side_icons"; // menu_side_dots
			?>">
	<div class="menu_side_inner"></div>
	
</div><!-- /.menu_side_wrap -->