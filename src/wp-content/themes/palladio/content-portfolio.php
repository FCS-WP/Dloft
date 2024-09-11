<?php
/**
 * The Portfolio template to display the content
 *
 * Used for index/archive/search.
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

$palladio_blog_style = explode('_', palladio_get_theme_option('blog_style'));
$palladio_columns = empty($palladio_blog_style[1]) ? 2 : max(2, $palladio_blog_style[1]);
$palladio_post_format = get_post_format();
$palladio_post_format = empty($palladio_post_format) ? 'standard' : str_replace('post-format-', '', $palladio_post_format);
$palladio_animation = palladio_get_theme_option('blog_animation');

?><article id="post-<?php the_ID(); ?>" 
	<?php post_class( 'post_item post_layout_portfolio post_layout_portfolio_'.esc_attr($palladio_columns).' post_format_'.esc_attr($palladio_post_format).(is_sticky() && !is_paged() ? ' sticky' : '') ); ?>
	<?php echo (!palladio_is_off($palladio_animation) ? ' data-animation="'.esc_attr(palladio_get_animation_classes($palladio_animation)).'"' : ''); ?>>
	<?php

	// Sticky label
	if ( is_sticky() && !is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

	$palladio_image_hover = palladio_get_theme_option('image_hover');
	// Featured image
	palladio_show_post_featured(array(
		'thumb_size' => palladio_get_thumb_size(strpos(palladio_get_theme_option('body_style'), 'full')!==false || $palladio_columns < 3 ? 'masonry-big' : 'masonry'),
		'show_no_image' => true,
		'class' => $palladio_image_hover == 'dots' ? 'hover_with_info' : '',
		'post_info' => $palladio_image_hover == 'dots' ? '<div class="post_info">'.esc_html(get_the_title()).'</div>' : ''
	));
	?>
</article>