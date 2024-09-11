<?php
/**
 * The template to display the widgets area in the header
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

// Header sidebar
$palladio_header_name = palladio_get_theme_option('header_widgets');
$palladio_header_present = !palladio_is_off($palladio_header_name) && is_active_sidebar($palladio_header_name);
if ($palladio_header_present) { 
	palladio_storage_set('current_sidebar', 'header');
	$palladio_header_wide = palladio_get_theme_option('header_wide');
	ob_start();
	if ( is_active_sidebar($palladio_header_name) ) {
		dynamic_sidebar($palladio_header_name);
	}
	$palladio_widgets_output = ob_get_contents();
	ob_end_clean();
	if (!empty($palladio_widgets_output)) {
		$palladio_widgets_output = preg_replace("/<\/aside>[\r\n\s]*<aside/", "</aside><aside", $palladio_widgets_output);
		$palladio_need_columns = strpos($palladio_widgets_output, 'columns_wrap')===false;
		if ($palladio_need_columns) {
			$palladio_columns = max(0, (int) palladio_get_theme_option('header_columns'));
			if ($palladio_columns == 0) $palladio_columns = min(6, max(1, substr_count($palladio_widgets_output, '<aside ')));
			if ($palladio_columns > 1)
				$palladio_widgets_output = preg_replace("/class=\"widget /", "class=\"column-1_".esc_attr($palladio_columns).' widget ', $palladio_widgets_output);
			else
				$palladio_need_columns = false;
		}
		?>
		<div class="header_widgets_wrap widget_area<?php echo !empty($palladio_header_wide) ? ' header_fullwidth' : ' header_boxed'; ?>">
			<div class="header_widgets_inner widget_area_inner">
				<?php 
				if (!$palladio_header_wide) { 
					?><div class="content_wrap"><?php
				}
				if ($palladio_need_columns) {
					?><div class="columns_wrap"><?php
				}
				do_action( 'palladio_action_before_sidebar' );
				palladio_show_layout($palladio_widgets_output);
				do_action( 'palladio_action_after_sidebar' );
				if ($palladio_need_columns) {
					?></div>	<!-- /.columns_wrap --><?php
				}
				if (!$palladio_header_wide) {
					?></div>	<!-- /.content_wrap --><?php
				}
				?>
			</div>	<!-- /.header_widgets_inner -->
		</div>	<!-- /.header_widgets_wrap -->
		<?php
	}
}
?>