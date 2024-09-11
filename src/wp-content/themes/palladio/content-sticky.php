<?php
/**
 * The Sticky template to display the sticky posts
 *
 * Used for index/archive
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

$palladio_columns = max(1, min(3, count(get_option( 'sticky_posts' ))));
$palladio_post_format = get_post_format();
$palladio_post_format = empty($palladio_post_format) ? 'standard' : str_replace('post-format-', '', $palladio_post_format);
$palladio_animation = palladio_get_theme_option('blog_animation');

?><div class="column-1_<?php echo esc_attr($palladio_columns); ?>"><article id="post-<?php the_ID(); ?>" 
	<?php post_class( 'post_item post_layout_sticky post_format_'.esc_attr($palladio_post_format) ); ?>
	<?php echo (!palladio_is_off($palladio_animation) ? ' data-animation="'.esc_attr(palladio_get_animation_classes($palladio_animation)).'"' : ''); ?>
	>

	<?php
	if ( is_sticky() && is_home() && !is_paged() ) {
        ?><span class="post_label label_sticky"><?php esc_html_e('Sticky Post', 'palladio'); ?><span class="label_sticky_line"></span></span><?php
	}

	// Featured image
	palladio_show_post_featured(array(
		'thumb_size' => palladio_get_thumb_size($palladio_columns==1 ? 'big' : ($palladio_columns==2 ? 'med' : 'avatar'))
	));

	if ( !in_array($palladio_post_format, array('link', 'aside', 'status', 'quote')) ) {
		?>
		<div class="post_header entry-header">
			<?php
			// Post title
			the_title( sprintf( '<h5 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h5>' );

			// Post meta
			palladio_show_post_meta(apply_filters('palladio_filter_post_meta_args', array(
                'components' => 'categories,author,date',
                'counters' => ''
            ), 'sticky', $palladio_columns));
			?>
		</div><!-- .entry-header -->
		<?php
	}
	?>
</article></div>