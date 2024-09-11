<?php
/**
 * The Footer: widgets area, logo, footer menu and socials
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

						// Widgets area inside page content
						palladio_create_widgets_area('widgets_below_content');
						?>				
					</div><!-- </.content> -->

					<?php
					// Show main sidebar
					get_sidebar();

					// Widgets area below page content
					palladio_create_widgets_area('widgets_below_page');

					$palladio_body_style = palladio_get_theme_option('body_style');
					if ($palladio_body_style != 'fullscreen') {
						?></div><!-- </.content_wrap> --><?php
					}
					?>
			</div><!-- </.page_content_wrap> -->

			<?php
			// Footer
			$palladio_footer_style = palladio_get_theme_option("footer_style");
			if (strpos($palladio_footer_style, 'footer-custom-')===0)
				$palladio_footer_style = palladio_is_layouts_available() ? 'footer-custom' : 'footer-default';
			get_template_part( "templates/{$palladio_footer_style}");
			?>

		</div><!-- /.page_wrap -->

	</div><!-- /.body_wrap -->

	<?php if (palladio_is_on(palladio_get_theme_option('debug_mode')) && palladio_get_file_dir('images/makeup.jpg')!='') { ?>
		<img src="<?php echo esc_url(palladio_get_file_url('images/makeup.jpg')); ?>" id="makeup">
	<?php } ?>

	<?php wp_footer(); ?>

</body>
</html>