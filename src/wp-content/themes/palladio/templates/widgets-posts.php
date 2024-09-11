<?php
/**
 * The template to display posts in widgets and/or in the search results
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

$palladio_post_id    = get_the_ID();
$palladio_post_date  = palladio_get_date();
$palladio_post_title = get_the_title();
$palladio_post_link  = get_permalink();
$palladio_post_author_id   = get_the_author_meta('ID');
$palladio_post_author_name = get_the_author_meta('display_name');
$palladio_post_author_url  = get_author_posts_url($palladio_post_author_id, '');

$palladio_args = get_query_var('palladio_args_widgets_posts');
$palladio_show_date = isset($palladio_args['show_date']) ? (int) $palladio_args['show_date'] : 1;
$palladio_show_image = isset($palladio_args['show_image']) ? (int) $palladio_args['show_image'] : 1;
$palladio_show_author = isset($palladio_args['show_author']) ? (int) $palladio_args['show_author'] : 1;
$palladio_show_counters = isset($palladio_args['show_counters']) ? (int) $palladio_args['show_counters'] : 1;
$palladio_show_categories = isset($palladio_args['show_categories']) ? (int) $palladio_args['show_categories'] : 1;

$palladio_output = palladio_storage_get('palladio_output_widgets_posts');

$palladio_post_counters_output = '';
if ( $palladio_show_counters ) {
	$palladio_post_counters_output = '<span class="post_info_item post_info_counters">'
								. palladio_get_post_counters('comments')
							. '</span>';
}


$palladio_output .= '<article class="post_item with_thumb">';

if ($palladio_show_image) {
	$palladio_post_thumb = get_the_post_thumbnail($palladio_post_id, palladio_get_thumb_size('tiny'), array(
		'alt' => the_title_attribute( array( 'echo' => false ) )
	));
	if ($palladio_post_thumb) $palladio_output .= '<div class="post_thumb">' . ($palladio_post_link ? '<a href="' . esc_url($palladio_post_link) . '">' : '') . ($palladio_post_thumb) . ($palladio_post_link ? '</a>' : '') . '</div>';
}

$palladio_output .= '<div class="post_content">'
			. ($palladio_show_categories 
					? '<div class="post_categories">'
						. palladio_get_post_categories()
						. $palladio_post_counters_output
						. '</div>' 
					: '')
			. '<h6 class="post_title">' . ($palladio_post_link ? '<a href="' . esc_url($palladio_post_link) . '">' : '') . ($palladio_post_title) . ($palladio_post_link ? '</a>' : '') . '</h6>'
			. apply_filters('palladio_filter_get_post_info', 
								'<div class="post_info">'
									. ($palladio_show_date 
										? '<span class="post_info_item post_info_posted">'
											. ($palladio_post_link ? '<a href="' . esc_url($palladio_post_link) . '" class="post_info_date">' : '') 
											. esc_html($palladio_post_date) 
											. ($palladio_post_link ? '</a>' : '')
											. '</span>'
										: '')
									. ($palladio_show_author 
										? '<span class="post_info_item post_info_posted_by">' 
											. esc_html__('by', 'palladio') . ' ' 
											. ($palladio_post_link ? '<a href="' . esc_url($palladio_post_author_url) . '" class="post_info_author">' : '') 
											. esc_html($palladio_post_author_name) 
											. ($palladio_post_link ? '</a>' : '') 
											. '</span>'
										: '')
									. (!$palladio_show_categories && $palladio_post_counters_output
										? $palladio_post_counters_output
										: '')
								. '</div>')
		. '</div>'
	. '</article>';
palladio_storage_set('palladio_output_widgets_posts', $palladio_output);
?>