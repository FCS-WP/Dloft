<?php
/**
 * The template for homepage posts with "Chess" style
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

palladio_storage_set('blog_archive', true);

get_header(); 

if (have_posts()) {

	echo get_query_var('blog_archive_start');

	$palladio_stickies = is_home() ? get_option( 'sticky_posts' ) : false;
	$palladio_sticky_out = palladio_get_theme_option('sticky_style')=='columns' 
							&& is_array($palladio_stickies) && count($palladio_stickies) > 0 && get_query_var( 'paged' ) < 1;
	if ($palladio_sticky_out) {
		?><div class="sticky_wrap columns_wrap"><?php	
	}
	if (!$palladio_sticky_out) {
		?><div class="chess_wrap posts_container"><?php
	}
	while ( have_posts() ) { the_post(); 
		if ($palladio_sticky_out && !is_sticky()) {
			$palladio_sticky_out = false;
			?></div><div class="chess_wrap posts_container"><?php
		}
		get_template_part( 'content', $palladio_sticky_out && is_sticky() ? 'sticky' :'chess' );
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