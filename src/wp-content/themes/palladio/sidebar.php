<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

if (palladio_sidebar_present()) {
	ob_start();
	$palladio_sidebar_name = palladio_get_theme_option('sidebar_widgets');
	palladio_storage_set('current_sidebar', 'sidebar');
	if ( is_active_sidebar($palladio_sidebar_name) ) {
		dynamic_sidebar($palladio_sidebar_name);
	}
	$palladio_out = trim(ob_get_contents());
	ob_end_clean();
	if (!empty($palladio_out)) {
		$palladio_sidebar_position = palladio_get_theme_option('sidebar_position');
		?>
		<div class="sidebar <?php echo esc_attr($palladio_sidebar_position); ?> widget_area<?php if (!palladio_is_inherit(palladio_get_theme_option('sidebar_scheme'))) echo ' scheme_'.esc_attr(palladio_get_theme_option('sidebar_scheme')); ?>" role="complementary">
			<div class="sidebar_inner">
				<?php
				do_action( 'palladio_action_before_sidebar' );
				palladio_show_layout(preg_replace("/<\/aside>[\r\n\s]*<aside/", "</aside><aside", $palladio_out));
				do_action( 'palladio_action_after_sidebar' );
				?>
			</div><!-- /.sidebar_inner -->
		</div><!-- /.sidebar -->
		<?php
	}
}
?>