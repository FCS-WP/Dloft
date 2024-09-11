<?php
/**
 * The template 'Style 1' to displaying related posts
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

$palladio_link = get_permalink();
$palladio_post_format = get_post_format();
$palladio_post_format = empty($palladio_post_format) ? 'standard' : str_replace('post-format-', '', $palladio_post_format);
?><div id="post-<?php the_ID(); ?>" 
	<?php post_class( 'related_item related_item_style_1 post_format_'.esc_attr($palladio_post_format) ); ?>><?php
	palladio_show_post_featured(array(
		'thumb_size' => palladio_get_thumb_size( (int) palladio_get_theme_option('related_posts') == 1 ? 'huge' : 'big' ),
		'show_no_image' => false,
		'singular' => false,
		'post_info' => '<div class="post_header entry-header">'
							. '<div class="post_categories">' . palladio_get_post_categories('') . '</div>'
							. '<h6 class="post_title entry-title"><a href="' . esc_url($palladio_link) . '">' . wp_kses_post( get_the_title() ) . '</a></h6>'
							. (in_array(get_post_type(), array('post', 'attachment'))
									? '<span class="post_date"><a href="' . esc_url($palladio_link) . '">' . palladio_get_date() . '</a></span>'
									: '')
						. '</div>'
		)
	);
?></div>