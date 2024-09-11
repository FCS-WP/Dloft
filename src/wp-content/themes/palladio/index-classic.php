<?php
/**
 * The template for homepage posts with "Classic" style
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

palladio_storage_set('blog_archive', true);


get_header(); 

if (have_posts()) {

	echo get_query_var('blog_archive_start');

	$palladio_classes = 'posts_container '
						. (substr(palladio_get_theme_option('blog_style'), 0, 7) == 'classic' ? 'columns_wrap columns_padding_bottom' : 'masonry_wrap');
	$palladio_stickies = is_home() ? get_option( 'sticky_posts' ) : false;
	$palladio_sticky_out = palladio_get_theme_option('sticky_style')=='columns' 
							&& is_array($palladio_stickies) && count($palladio_stickies) > 0 && get_query_var( 'paged' ) < 1;
	if ($palladio_sticky_out) {
		?><div class="sticky_wrap columns_wrap"><?php	
	}
	if (!$palladio_sticky_out) {
		if (palladio_get_theme_option('first_post_large') && !is_paged() && !in_array(palladio_get_theme_option('body_style'), array('fullwide', 'fullscreen'))) {
			the_post();
			get_template_part( 'content', 'excerpt' );
		}
		
		?><div class="<?php echo esc_attr($palladio_classes); ?>"><?php
	}
	while ( have_posts() ) { the_post(); 
		if ($palladio_sticky_out && !is_sticky()) {
			$palladio_sticky_out = false;
			?></div><div class="<?php echo esc_attr($palladio_classes); ?>"><?php
		}
		get_template_part( 'content', $palladio_sticky_out && is_sticky() ? 'sticky' : 'classic' );
	}
	
	?></div><?php

	palladio_show_pagination();

	echo get_query_var('blog_archive_end');

} else {

	if ( is_search() )
		get_template_part( 'content', 'none-search' );
	else
		get_template_part( 'content', 'none-archive' );

}

get_footer();
?>