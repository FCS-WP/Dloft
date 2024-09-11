<?php
/**
 * The Gallery template to display posts
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
$palladio_image = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'full' );

?><article id="post-<?php the_ID(); ?>" 
	<?php post_class( 'post_item post_layout_portfolio post_layout_gallery post_layout_gallery_'.esc_attr($palladio_columns).' post_format_'.esc_attr($palladio_post_format) ); ?>
	<?php echo (!palladio_is_off($palladio_animation) ? ' data-animation="'.esc_attr(palladio_get_animation_classes($palladio_animation)).'"' : ''); ?>
	data-size="<?php if (!empty($palladio_image[1]) && !empty($palladio_image[2])) echo intval($palladio_image[1]) .'x' . intval($palladio_image[2]); ?>"
	data-src="<?php if (!empty($palladio_image[0])) echo esc_url($palladio_image[0]); ?>"
	>

	<?php

	// Sticky label
	if ( is_sticky() && !is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

	// Featured image
	$palladio_image_hover = 'icon';
	if (in_array($palladio_image_hover, array('icons', 'zoom'))) $palladio_image_hover = 'dots';
	$palladio_components = palladio_is_inherit(palladio_get_theme_option_from_meta('meta_parts')) 
								? 'categories,date,counters,share'
								: palladio_array_get_keys_by_value(palladio_get_theme_option('meta_parts'));
	$palladio_counters = palladio_is_inherit(palladio_get_theme_option_from_meta('counters')) 
								? 'comments'
								: palladio_array_get_keys_by_value(palladio_get_theme_option('counters'));
	palladio_show_post_featured(array(
		'hover' => $palladio_image_hover,
		'thumb_size' => palladio_get_thumb_size( strpos(palladio_get_theme_option('body_style'), 'full')!==false || $palladio_columns < 3 ? 'masonry-big' : 'masonry' ),
		'thumb_only' => true,
		'show_no_image' => true,
		'post_info' => '<div class="post_details">'
							. '<h2 class="post_title"><a href="'.esc_url(get_permalink()).'">'. esc_html(get_the_title()) . '</a></h2>'
							. '<div class="post_description">'
								. (!empty($palladio_components)
										? palladio_show_post_meta(apply_filters('palladio_filter_post_meta_args', array(
											'components' => $palladio_components,
											'counters' => $palladio_counters,
											'seo' => false,
											'echo' => false
											), $palladio_blog_style[0], $palladio_columns))
										: '')
								. '<div class="post_description_content">'
									. apply_filters('the_excerpt', get_the_excerpt())
								. '</div>'
								. '<a href="'.esc_url(get_permalink()).'" class="theme_button post_readmore"><span class="post_readmore_label">' . esc_html__('Learn more', 'palladio') . '</span></a>'
							. '</div>'
						. '</div>'
	));
	?>
</article>