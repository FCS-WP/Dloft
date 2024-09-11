<?php
/**
 * The Header: Logo and main menu
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js scheme_<?php
										 // Class scheme_xxx need in the <html> as context for the <body>!
										 echo esc_attr(palladio_get_theme_option('color_scheme'));
										 ?>">
<head>
	<?php wp_head(); ?>
</head>

<body <?php	body_class(); ?>>

	<?php wp_body_open(); ?>

	<?php do_action( 'palladio_action_before' ); ?>

	<div class="body_wrap">

		<div class="page_wrap"><?php

			// Desktop header
			$palladio_header_style = palladio_get_theme_option("header_style");
			if (strpos($palladio_header_style, 'header-custom-')===0) 
				$palladio_header_style = palladio_is_layouts_available() ? 'header-custom' : 'header-default';
			get_template_part( "templates/{$palladio_header_style}");

			// Side menu
            $palladio_menu_style = in_array(palladio_get_theme_option('menu_style'), array('left', 'right'));
			if ($palladio_menu_style) {
				get_template_part( 'templates/header-navi-side' );
			} elseif (!$palladio_menu_style && palladio_get_theme_option('body_style') == 'boxed') {
                get_template_part( 'templates/header-navi-side-top' );
            }

			// Mobile header
			get_template_part( 'templates/header-mobile');
			?>

			<div class="page_content_wrap scheme_<?php echo esc_attr(palladio_get_theme_option('color_scheme')); ?>">

				<?php if (palladio_get_theme_option('body_style') != 'fullscreen') { ?>
				<div class="content_wrap">
				<?php } ?>

					<?php
					// Widgets area above page content
					palladio_create_widgets_area('widgets_above_page');
					?>				

					<div class="content">
						<?php
						// Widgets area inside page content
						palladio_create_widgets_area('widgets_above_content');
						?>				
