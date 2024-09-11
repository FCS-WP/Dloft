<?php
/**
 * The template to display the widgets area in the footer
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0.10
 */

// Footer sidebar
$palladio_footer_name = palladio_get_theme_option('footer_widgets');
$palladio_footer_present = !palladio_is_off($palladio_footer_name) && is_active_sidebar($palladio_footer_name);
if ($palladio_footer_present) { 
	palladio_storage_set('current_sidebar', 'footer');
	$palladio_footer_wide = palladio_get_theme_option('footer_wide');
	ob_start();
	if ( is_active_sidebar($palladio_footer_name) ) {
		dynamic_sidebar($palladio_footer_name);
	}
	$palladio_out = trim(ob_get_contents());
	ob_end_clean();
	if (!empty($palladio_out)) {
		$palladio_out = preg_replace("/<\\/aside>[\r\n\s]*<aside/", "</aside><aside", $palladio_out);
		$palladio_need_columns = true;	//or check: strpos($palladio_out, 'columns_wrap')===false;
		if ($palladio_need_columns) {
			$palladio_columns = max(0, (int) palladio_get_theme_option('footer_columns'));
			if ($palladio_columns == 0) $palladio_columns = min(4, max(1, substr_count($palladio_out, '<aside ')));
			if ($palladio_columns > 1)
				$palladio_out = preg_replace("/class=\"widget /", "class=\"column-1_".esc_attr($palladio_columns).' widget ', $palladio_out);
			else
				$palladio_need_columns = false;
		}
		?>
		<div class="footer_widgets_wrap widget_area<?php echo !empty($palladio_footer_wide) ? ' footer_fullwidth' : ''; ?> sc_layouts_row  sc_layouts_row_type_normal">
			<div class="footer_widgets_inner widget_area_inner">
				<?php 
				if (!$palladio_footer_wide) { 
					?><div class="content_wrap"><?php
				}
				if ($palladio_need_columns) {
					?><div class="columns_wrap"><?php
				}
				do_action( 'palladio_action_before_sidebar' );
				palladio_show_layout($palladio_out);
				do_action( 'palladio_action_after_sidebar' );
				if ($palladio_need_columns) {
					?></div><!-- /.columns_wrap --><?php
				}
				if (!$palladio_footer_wide) {
					?></div><!-- /.content_wrap --><?php
				}
				?>
			</div><!-- /.footer_widgets_inner -->
		</div><!-- /.footer_widgets_wrap -->
		<?php
	}
}
?>